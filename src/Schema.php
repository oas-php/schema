<?php declare(strict_types=1);

namespace OAS;

use ArrayAccess;
use Biera\ArrayAccessor;
use JsonSerializable;
use LogicException;
use OAS\Resolver\Resolver;
use OAS\Resolver\UnreachableFragmentError;
use OAS\Schema\Factory;
use OAS\Schema\Type;
use OAS\Schema\Vocabulary;
use OAS\Utils\Node;
use OAS\Utils\Serializable;
use RuntimeException;
use stdClass;
use function Biera\retrieveByPath;
use function Biera\pathSegments;

// TODO: review connectivity (perhaps it does not have to extend Node?)
// so: Node is necessary to provide connectivity between OAS\Schema and OAS\Document\*
// connectivity requirements:
//  * locate nodes by
//      * absolute path (through root), like: "/$defs/foo"
//.     * anchors ("plain fragments"), like: "foo"

// perhaps there is a need to have connectivity features work twofold:
//  * work on entire resolved resource;  e.g find|getGlobally()
//  * work on single resource (searching for anchors should be done in this mode); find|get()

class Schema extends Node implements JsonSerializable, ArrayAccess
{
    use ArrayAccessor, Serializable;
    use Vocabulary\Core;
    use Vocabulary\Applicator;
    use Vocabulary\Validation;
    use Vocabulary\MetaData;
    use Vocabulary\Format;
    use Vocabulary\Unevaluated;

    private Schema|bool|null $reference = null;

    /** @var Schema|bool|array<string, Schema|bool>|null  */
    private Schema|bool|array|null $dynamicReferences = null;

    private ?string $resolvedId = null;

    public static int $instancesCount = 0;

    /**
     * @param ?array<string, \OAS\Schema|bool> $_defs
     * @param ?array<string, bool> $_vocabulary
     * @param ?array<int, \OAS\Schema|bool> $allOf
     * @param ?array<int, \OAS\Schema|bool> $anyOf
     * @param ?array<int, \OAS\Schema|bool> $oneOf
     * @param ?array<string, \OAS\Schema|bool> $dependentSchemas
     * @param ?array<int, \OAS\Schema|bool> $prefixItems
     * @param ?array<string, \OAS\Schema|bool> $properties
     * @param ?array<string, \OAS\Schema|bool> $patternProperties
     * @param Type|array<int, Type>|null $type
     * @param ?array<int, mixed> $enum
     * @param ?array<string> $required
     * @param ?array<string, array<int, string>> $dependentRequired
     */
    public function __construct(
        // core
        ?string $_schema = null,
        ?string $_id = null,
        ?string $_ref = null,
        ?array  $_defs = null,
        ?string $_comment = null,
        ?array $_vocabulary = null,
        ?string $_dynamicRef = null,
        ?string $_dynamicAnchor = null,
        ?string $_anchor = null,
        // applicator
        ?array $allOf = null,
        ?array $anyOf = null,
        ?array $oneOf = null,
        Schema|bool|null $not = null,
        Schema|bool|null $if = null,
        Schema|bool|null $then = null,
        Schema|bool|null $else = null,
        ?array $dependentSchemas = null,
        ?array $prefixItems = null,
        Schema|bool|null $items = null,
        Schema|bool|null $contains = null,
        ?array $properties = null,
        ?array $patternProperties = null,
        Schema|bool|null $additionalProperties = null,
        Schema|bool|null $propertyNames = null,
        // validation
        Type|array|null $type = null,
        ?array $enum = null,
        mixed $const = null,
        int|float|null $multipleOf = null,
        int|float|null $maximum = null,
        int|float|null $exclusiveMaximum = null,
        int|float|null $minimum = null,
        int|float|null $exclusiveMinimum = null,
        ?int $maxLength = null,
        ?int $minLength = null,
        ?string $pattern = null,
        ?int $maxItems = null,
        ?int $minItems = null,
        ?bool $uniqueItems = null,
        ?int $maxContains = null,
        ?int $minContains = null,
        ?int $maxProperties = null,
        ?int $minProperties = null,
        ?array $required = null,
        ?array $dependentRequired = null,
        // meta
        ?string $title = null,
        ?string $description = null,
        mixed $default = null,
        ?bool $deprecated = null,
        ?bool $readOnly = null,
        ?bool $writeOnly = null,
        ?array $examples = null,
        // format
        ?string $format = null,
        // unevaluated
        Schema|bool|null $unevaluatedProperties = null,
        Schema|bool|null $unevaluatedItems = null
    ) {
        self::$instancesCount++;
        // core
        $this->_id = $_id;
        $this->_schema = $_schema;
        $this->_anchor = $_anchor;
        $this->_ref = $_ref;
        $this->_dynamicRef = $_dynamicRef;
        $this->_dynamicAnchor = $_dynamicAnchor;
        if (!is_null($_vocabulary)) $this->setVocabulary($_vocabulary);
        $this->_comment = $_comment;
        if (!is_null($_defs)) $this->setDefs($_defs);
        // applicator
        if (!is_null($allOf)) $this->setAllOf($allOf);
        if (!is_null($anyOf)) $this->setAnyOf($anyOf);
        if (!is_null($oneOf)) $this->setOneOf($oneOf);
        if (!is_null($not)) $this->setNot($not);
        if (!is_null($if)) $this->setIf($if);
        if (!is_null($then)) $this->setThen($then);
        if (!is_null($else)) $this->setElse($else);
        if (!is_null($dependentSchemas)) $this->setDependentSchemas($dependentSchemas);
        if (!is_null($prefixItems)) $this->setPrefixItems($prefixItems);
        if (!is_null($items)) $this->setItems($items);
        if (!is_null($contains)) $this->setContains($contains);
        if (!is_null($properties)) $this->setProperties($properties);
        if (!is_null($patternProperties)) $this->setPatternProperties($patternProperties);
        if (!is_null($additionalProperties)) $this->setAdditionalProperties($additionalProperties);
        if (!is_null($propertyNames)) $this->setPropertyNames($propertyNames);
        // validation
        if (!is_null($type)) $this->setType($type);
        if (!is_null($enum)) $this->setEnum($enum);
        $this->const = $const;
        if (!is_null($multipleOf)) $this->setMultipleOf($multipleOf);
        $this->maximum = $maximum;
        $this->exclusiveMaximum = $exclusiveMaximum;
        $this->minimum = $minimum;
        $this->exclusiveMinimum = $exclusiveMinimum;
        if (!is_null($maxLength)) $this->setMaxLength($maxLength);
        if (!is_null($minLength)) $this->setMinLength($minLength);
        $this->pattern = $pattern;
        if (!is_null($maxItems)) $this->setMaxItems($maxItems);
        if (!is_null($minItems)) $this->setMinItems($minItems);
        $this->uniqueItems = $uniqueItems;
        if (!is_null($maxContains)) $this->setMaxContains($maxContains);
        if (!is_null($minContains)) $this->setMinContains($minContains);
        if (!is_null($maxProperties)) $this->setMaxProperties($maxProperties);
        if (!is_null($minProperties)) $this->setMinProperties($minProperties);
        if (!is_null($required)) $this->setRequired($required);
        if (!is_null($dependentRequired)) $this->setDependentRequired($dependentRequired);
        // metadata
        $this->title = $title;
        $this->description = $description;
        $this->default = $default;
        $this->deprecated = $deprecated;
        $this->readOnly = $readOnly;
        $this->writeOnly = $writeOnly;
        $this->examples = $examples;
        // format
        $this->format = $format;
        // unevaluated
        if (!is_null($unevaluatedProperties)) $this->setUnevaluatedProperties($unevaluatedProperties);
        if (!is_null($unevaluatedItems)) $this->setUnevaluatedItems($unevaluatedItems);
    }

    // TODO: is it really necessary? :)
    public function __invoke(string $JSONPointer): mixed
    {
        return $this->find($JSONPointer);
    }

    public function resolveId(string $id): void
    {
        $this->resolvedId = $id;
    }

    public function getResolvedId(): ?string
    {
        return $this->resolvedId;
    }

    /**
     * @throws LogicException
     * @throws RuntimeException
     */
    public function resolveReference(Schema|bool $reference): void
    {
        if ($this->_ref === null) {
            // TODO: nicer message please
            throw new LogicException('The schema does not  can not be resolved when schema does not a "$ref" keyword');
        }

        if ($this->reference !== null) {
            throw new RuntimeException('The reference is already resolved');
        }

        $this->reference = $reference;
    }

    public function getResolvedReference(): Schema|bool|null
    {
        if ($this->reference !== null) {
            return $this->reference;
        }

        if ($this->_ref !== null) {
            $ref = $this->_ref;

            if (!str_starts_with($ref, '#')) {
                // TODO: dedicated exception?
                throw new RuntimeException(
                    sprintf(
                        'Reference not resolved (it could  be resolved by %s with %s provided)',
                        Factory::class,
                        Resolver::class
                    )
                );
            }

            try {
                $fragment = substr($ref, 1);
                $referencedSchema = $this->getRoot()->find($fragment);

                if (!($referencedSchema instanceof Schema || is_bool($referencedSchema))) {
                    // TODO: throw a dedicated exception
                    throw new RuntimeException('$ref must point to a valid schema');
                }

                $this->reference = $referencedSchema;
            } catch (RuntimeException) {
                // TODO: check if resolver throws it with "#" prefixed!
                throw new UnreachableFragmentError($fragment);
            }
        }

        return $this->reference;
    }

    /**
     * @param Schema|array<string, Schema> $dynamicReferences
     */
    public function resolveDynamicReference(Schema|bool|array $dynamicReferences): void
    {
        if ($this->dynamicReferences !== null) {
            throw new RuntimeException('The dynamic reference is already resolved');
        }

        $this->dynamicReferences = $dynamicReferences;
    }

    /**
     * @return Schema|bool|array<string, Schema|bool>|null
     */
    public function getDynamicReference(): Schema|bool|array|null
    {
        return $this->dynamicReferences;
    }

    /**
     * @throws RuntimeException
     */
    public function find(string $path): mixed
    {
        if (str_starts_with($path, '/')) {
            return parent::find($path);
        }

        /** @var Schema $schema */
        foreach ($this->getRoot() as $schema) {
            if ($schema->getAnchor() === $path) {
                return $schema;
            }
        }

        // TODO throw dedicated exception!
        throw new RuntimeException("The path \"{$path}\" does not exist");
    }

    // TODO: verify usage and perhaps replace by Node::find?
    public function get(string $path): mixed
    {
        return retrieveByPath($this, array_map('OAS\Resolver\decode', pathSegments($path)));
    }

    // TODO: review Biera\ArrayAccess ;-)
    public function offsetExists($offset): bool
    {
        return in_array(
            self::normalizePropertyName($offset),
            $this->getReflectedProperties()
        );
    }

    public function offsetGet($offset): mixed
    {
        return $this->{self::normalizePropertyName($offset)};
    }

    private static function normalizePropertyName(string $propertyName): string
    {
        if (!empty($propertyName) && '$' == $propertyName[0]) {
            $propertyName[0] = '_';
        }

        return  $propertyName;
    }

    private static function denormalizePropertyNames(array $properties): array
    {
        return array_combine(
            array_map(
                function ($propertyName) {
                    // TODO: stop using empty ("0" is "empty"), better use strlen() > 0
                    if (!empty($propertyName) && '_' == $propertyName[0]) {
                        $propertyName[0] = '$';
                    }

                    return $propertyName;
                },
                array_keys($properties)
            ),
            $properties
        );
    }

    private function setChild(Schema|bool $schema, $path): void
    {
        if ($schema instanceof Schema) {
            $this->__connect($schema, [$path]);
        }
    }

    /**
     * @param array<int, \OAS\Schema|bool> $schemas
     * @param array<int, string> $path
     */
    private function setChildren(array $schemas, array $path = []): void
    {
        foreach ($schemas as $pathSegment => $schema) {
            if ($schema instanceof Schema) {
                $this->__connect($schema, [...$path, $pathSegment]);
            }
        }
    }

    public function jsonSerialize(): stdClass|array|bool
    {
        $exclude = ['reference', 'dynamicReferences', 'resolvedId'];

        $properties = array_filter(
            get_object_vars($this),
            fn ($value, $property) =>
                !is_null($value)
                && !str_starts_with($property, '__')
                && !in_array($property, $exclude),
            ARRAY_FILTER_USE_BOTH
        );

        return empty($properties)
            ? new stdClass()
            : self::denormalizePropertyNames($properties);
    }

    // TODO: is this necessary?
    public function path(): string
    {
       return join('/', $this->getRootPath());
    }
}
