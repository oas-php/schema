<?php declare(strict_types=1);

namespace OAS\Schema\Factory;

use OAS\Resolver\Resolver;
use OAS\Schema;
use OAS\Utils\Constructor\Constructor;
use OAS\Utils\Constructor\Dispatcher;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Return_;

// TODO: remove trailing nulls when dumping
class Dumper
{
    private ?Resolver $resolver;
    private Constructor $constructor;

    public function __construct(Resolver $resolver = null)
    {
        $this->resolver = $resolver;
        $dispatcher = new Dispatcher();
        $dispatcher->subscribe(
            new SchemaConstructionEventsSubscriber(false)
        );

        $this->constructor = new Constructor($dispatcher);
    }

    public function dumpFromPrimitives(array $primitives, string $path): void
    {
        $prettyPrinter = new PrettyPrinter\Standard();

        file_put_contents(
            $path,
            $prettyPrinter->prettyPrintFile(
                [
                    new Return_(
                        $this->getAstFromPrimitives($primitives)
                    )
                ]
            )
        );
    }

    /**
     * @param \stdClass|array|bool $primitives
     * @return Expr
     */
    public function getAstFromPrimitives($primitives): Expr
    {
        if (is_bool($primitives)) {
            return (new BuilderFactory)
                ->staticCall(Schema::class, 'createBooleanSchema', [$primitives]);
        }

        if ($primitives instanceof \stdClass) {
            $primitives = (array) $primitives;
        }

        if (!is_array($primitives)) {
            throw new \TypeError(
                'Parameter "primitives" must be of bool|array|\stdClass type'
            );
        }

        return $this->constructor->getAST(
            Schema::class,
            $this->resolver
                // TODO: check if it can be resolved to \stdClass
                ? $this->resolver
                    ->resolveDecoded($primitives)
                    ->denormalize(true)
                : $primitives
        );
    }
}
