<?php declare(strict_types=1);

namespace OAS\Schema;

use LogicException;
use OAS\Resolver\DynamicReference;
use OAS\Resolver\Node;
use OAS\Resolver\Reference;
use OAS\Resolver;
use OAS\Schema;
use OAS\Schema\Factory\ConstructorEventSubscriber;
use OAS\Utils\Constructor;
use OAS\Utils\ConstructorParametersResolver;
use OAS\Utils\ConstructorParametersResolver\Dispatcher;
use stdClass;
use function iter\filter;
use function OAS\Resolver\decode;

class Factory
{
    private ?Resolver $resolver;
    private Constructor $constructor;

    public function __construct(?Resolver $resolver = null)
    {
        $this->resolver = $resolver;
        $dispatcher = new Dispatcher();
        $dispatcher->subscribe(new ConstructorEventSubscriber());
        $this->constructor = new Constructor(
            new ConstructorParametersResolver($dispatcher)
        );
    }

    /**
     * @param stdClass|array<string, mixed>|bool $primitives
     */
    public function createFromPrimitives(stdClass|array|bool $primitives): Schema|bool
    {
        if (is_bool($primitives)) {
            return $primitives;
        }

        if ($primitives instanceof stdClass) {
            $primitives = (array) $primitives;
        }

        return $this->resolver !== null
            ? $this->constructAndResolve($this->resolver->resolveDecoded($primitives))
            : $this->constructor->construct(Schema::class, $primitives);
    }

    public function createFromUri(string $uri): Schema|bool
    {
        if (is_null($this->resolver)) {
            throw new LogicException(
                'Resolver not set: install php-oas\resolver package and provide instance of '
                . 'OAS\Resolver as a constructor parameter'
            );
        }

        return $this->constructAndResolve($this->resolver->resolve($uri));
    }

    /**
     * TODO: try to explain things in comments / separate doc
     *
     *  The $node always represents the data to build the schema to return.
     *  However, it might be just a sub-node of a node fetched from given location (in case of $refs with fragments).
     *  For optimization purposes (mainly to reuse Schema objects) the schema is not build directly from node, instead
     *  the node root is used to built schema/schemas for locations that were referenced anywhere in fully resolved schema.
     *
     * @param array<string, array<string, Schema>> $constructedSchemas
     */
    private function constructAndResolve(Node $node, array $constructedSchemas = []): Schema|bool
    {
        $uri = (string) $node->getUri()->withoutFragment();
        $fragment = $node->getUri()->getFragment() ?? '/';

        if (array_key_exists($uri, $constructedSchemas)) {
            return $this->findSchemaByUriAndFragment($uri, $fragment, $constructedSchemas);
        }

        foreach ($node->getRoot()->getProcessedNodeIterator() as $path => $processedNode) {
            $denormalized = $processedNode->denormalize();
            $constructedSchemas[$uri][$path] = is_bool($denormalized)
                ? $denormalized
                : $this->constructor->construct(Schema::class, $denormalized);
        }

        $schema = $this->findSchemaByUriAndFragment($uri, $fragment, $constructedSchemas);

        if ($schema instanceof Schema) {
                // remember that schema maps directly to node so no path correction is needed (is )
                foreach ($this->schemaWithReferenceIterator($schema) as $path => $schemaWithReference) {
                    $nodeWithReference = $node->find($path);
                    assert($nodeWithReference instanceof Node);

                    if ($schemaWithReference->hasRef()) {
                        $reference = $nodeWithReference->getChild('$ref');
                        assert($reference instanceof Reference);

                        $schemaWithReference->resolveReference(
                            $this->constructAndResolve(
                                $reference->getResolved(),
                                $constructedSchemas
                            )
                        );
                    }

                    if ($schemaWithReference->hasDynamicRef()) {
                        $reference = $nodeWithReference->getChild('$dynamicRef');
                        assert($reference instanceof DynamicReference);
                        $resolved = $reference->getResolved();

                        $schemaWithReference->resolveDynamicReference(
                            $resolved instanceof Node
                                ? $this->constructAndResolve($resolved, $constructedSchemas)
                                : array_map(
                                fn (Node $node) => $this->constructAndResolve($node, $constructedSchemas),
                                $resolved
                            )
                        );
                    }
                }
            }

        return $schema;
    }

    /**
     * @param array<string, array<string, Schema>> $constructedSchemas
     */
    private function findSchemaByUriAndFragment(string $uri, string $fragment, array $constructedSchemas): Schema|bool
    {
        assert(array_key_exists($uri, $constructedSchemas));

        foreach ($constructedSchemas[$uri] as $constructedAtPath => $schema) {
            if (str_starts_with(haystack: $fragment, needle: $constructedAtPath)) {
                $relativeFragment1 = $this->getRelativeFragment($fragment, $constructedAtPath);
                // more common path (shortest) first
                $relativeFragment2 = $this->getRelativeFragment($constructedAtPath, $fragment);
                $schema = $schema->find($relativeFragment2);
                assert($schema instanceof Schema);

                return $schema;
            }
        }

        // This should never have been thrown!
        // TODO: throw dedicated exception or at least provide meaningful message
        throw new LogicException();
    }

    /**
     *  This function is only called when str_starts_with($pathA, $pathB) == true
     *  so in fact, common prefix ($pathA) is removed from $pathB
     *
     *  the $pathA should the shorter one
     */
    private function getRelativeFragment(string $pathA, string $pathB): string
    {
        if ($pathA == '/') {
            return $pathB;
        }

        $withoutCommonPrefix = substr($pathB, strlen($pathA));

        return $withoutCommonPrefix == '' ? '/' : $withoutCommonPrefix;
    }

    /**
     * @return iterable<string, \OAS\Schema>
     */
    private function schemaWithReferenceIterator(Schema $schema): iterable
    {
        return filter(fn (Schema $subSchema) => $subSchema->hasRef() || $subSchema->hasDynamicRef(), $schema);
    }
}
