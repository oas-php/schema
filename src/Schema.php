<?php declare(strict_types=1);

namespace OAS;

use ArrayAccess;
use Biera\ArrayAccessor;
use JsonSerializable;
use OAS\Schema\Vocabulary;
use OAS\Utils\Node;
use OAS\Utils\Serializable;
use stdClass;
use function Biera\retrieveByPath;
use function Biera\pathSegments;

class Schema extends Node implements JsonSerializable, ArrayAccess
{
    use ArrayAccessor, Serializable;
    use Vocabulary\Core;
    use Vocabulary\MetaData;
    use Vocabulary\Format;
    use Vocabulary\Validation;
    use Vocabulary\Applicator;

    public const TYPE_NULL = 'null';
    public const TYPE_STRING = 'string';
    public const TYPE_NUMBER = 'number';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_ARRAY = 'array';
    public const TYPE_OBJECT = 'object';

    public const TYPES = [
        self::TYPE_NULL,
        self::TYPE_STRING,
        self::TYPE_NUMBER,
        self::TYPE_INTEGER,
        self::TYPE_BOOLEAN,
        self::TYPE_ARRAY,
        self::TYPE_OBJECT
    ];

    private mixed $example;
    private ?bool $alwaysValid = null;
    private ?bool $alwaysInvalid = null;

    /**
     * TODO: validate values like $minLength (must be a positive integer!)
     *
     * @param ?array<string, boolean> $_vocabulary
     * @param ?array<string, \OAS\Schema> $_defs
     * @param ?array<string> $required
     * @param ?array<string, array<int, string>> $dependentRequired
     * @param ?array<int, mixed> $enum
     * @param array<string>|string|null $type
     * @param array<int, \OAS\Schema>|\OAS\Schema|null $items
     * @param ?array<string, \OAS\Schema> $properties
     * @param ?array<string, \OAS\Schema> $patternProperties
     * @param ?array<string, \OAS\Schema> $dependentSchemas
     * @param ?array<int, \OAS\Schema> $allOf
     * @param ?array<int, \OAS\Schema> $anyOf
     * @param ?array<int, \OAS\Schema> $oneOf
     */
    public function __construct(
        // core
        ?string $_id = null,
        ?string $_schema = null,
        ?string $_anchor = null,
        ?string $_ref = null,
        ?string $_dynamicRef = null,
        ?string $_dynamicAnchor = null,
        ?array $_vocabulary = null,
        ?string $_comment = null,
        ?array  $_defs = null,
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
        // validation
        null|int|float $multipleOf = null,
        null|int|float $maximum = null,
        null|int|float $exclusiveMaximum = null,
        null|int|float $minimum = null,
        null|int|float $exclusiveMinimum = null,
        ?int $maxLength = null,
        ?int $minLength = null,
        ?string $pattern = null,
        ?int $minItems = null,
        ?int $maxItems = null,
        ?bool $uniqueItems = null,
        ?int $maxContains = null,
        ?int $minContains = null,
        ?int $maxProperties = null,
        ?int $minProperties = null,
        ?array $required = null,
        ?array $dependentRequired = null,
        mixed $const = null,
        ?array $enum = null,
        array|string|null $type = null,
        // applicator
        ?Schema $additionalItems = null,
        array|Schema|null $items = null,
        ?Schema $contains = null,
        // TODO: should bool type allowed? perhaps Schema::createBooleanSchema is enough?
        ?Schema $additionalProperties = null,
        ?array $properties = null,
        ?array $patternProperties = null,
        ?array $dependentSchemas = null,
        ?Schema $propertyNames = null,
        ?Schema $if = null,
        ?Schema $then = null,
        ?Schema $else = null,
        ?array $allOf = null,
        ?array $anyOf = null,
        ?array $oneOf = null,
        ?Schema $not = null
    ) {
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
        // validation
        $this->multipleOf = $multipleOf;
        $this->maximum = $maximum;
        $this->exclusiveMaximum = $exclusiveMaximum;
        $this->minimum = $minimum;
        $this->exclusiveMinimum = $exclusiveMinimum;
        $this->maxLength = $maxLength;
        $this->minLength = $minLength;
        $this->pattern = $pattern;
        $this->maxItems = $maxItems;
        $this->minItems = $minItems;
        $this->uniqueItems = $uniqueItems;
        $this->maxContains = $maxContains;
        $this->minContains = $minContains;
        $this->maxProperties = $maxProperties;
        $this->minProperties = $minProperties;
        if (!is_null($required)) $this->setRequired($required);
        if (!is_null($dependentRequired)) $this->setDependentRequired($dependentRequired);
        $this->const = $const;
        if (!is_null($enum)) $this->setEnum($enum);
        if (!is_null($type)) $this->setType($type);
        // applicator
        if (!is_null($additionalItems)) $this->setAdditionalItems($additionalItems);
        if (!is_null($items)) $this->setItems($items);
        if (!is_null($contains)) $this->setContains($contains);
        if (!is_null($additionalProperties)) $this->setAdditionalProperties($additionalProperties);
        $this->setProperties($properties);
        $this->setPatternProperties($patternProperties);
        $this->setDependentSchemas($dependentSchemas);
        $this->propertyNames = $propertyNames;
        $this->setIf($if);
        $this->setThen($then);
        $this->setElse($else);
        if (!is_null($allOf)) $this->setAllOf($allOf);
        if (!is_null($oneOf)) $this->setOneOf($oneOf);
        if (!is_null($anyOf)) $this->setAnyOf($anyOf);
        $this->setNot($not);
    }

    public static function createBooleanSchema(bool $value): self
    {
        $schema = new self();
        $schema->{$value ? 'alwaysValid' : 'alwaysInvalid'} = true;

        return $schema;
    }

    public static function createStringType(
        ?int $minLength = null,
        ?int $maxLength = null,
        ?string $format = null,
        ?string $pattern = null
    ): self
    {
        return new self(
            format: $format,
            maxLength: $maxLength,
            minLength: $minLength,
            pattern: $pattern,
            type: Schema::TYPE_STRING
        );
    }

    public static function createIntegerType(
        int|float|null $multipleOf = null,
        int|float|null $minimum = null,
        int|float|null $exclusiveMinimum = null,
        int|float|null $maximum = null,
        int|float|null $exclusiveMaximum = null
    ): self
    {
        return self::createNumericType(
            Schema::TYPE_INTEGER,
            $multipleOf,
            $minimum,
            $exclusiveMinimum,
            $maximum,
            $exclusiveMaximum
        );
    }

    public static function createNumberType(
        int|float|null $multipleOf = null,
        int|float|null $minimum = null,
        int|float|null $exclusiveMinimum = null,
        int|float|null $maximum = null,
        int|float|null $exclusiveMaximum = null
    ): self
    {
        return self::createNumericType(
            self::TYPE_NUMBER,
            $multipleOf,
            $minimum,
            $exclusiveMinimum,
            $maximum,
            $exclusiveMaximum
        );
    }

    private static function createNumericType(
        string $type,
        int|float|null $multipleOf = null,
        int|float|null $minimum = null,
        int|float|null $exclusiveMinimum = null,
        int|float|null $maximum = null,
        int|float|null $exclusiveMaximum = null
    ): self
    {
        return new self(
            multipleOf: $multipleOf,
            maximum: $maximum,
            exclusiveMaximum: $exclusiveMaximum,
            minimum: $minimum,
            exclusiveMinimum: $exclusiveMinimum,
            type: $type
        );
    }

    /**
     * @param array<int, Schema>|Schema|null $items
     */
    public static function createArrayType(
        array|Schema|null $items = null,
        ?Schema $additionalItems = null,
        ?int $minItems = null,
        ?int $maxItems = null,
        ?bool $uniqueItems = null,
        ?Schema $contains = null,
        ?int $maxContains = null,
        ?int $minContains = null
    ): self
    {
        return new self(
            minItems: $minItems,
            maxItems: $maxItems,
            uniqueItems: $uniqueItems,
            maxContains: $maxContains,
            minContains: $minContains,
            type: self::TYPE_ARRAY,
            additionalItems: $additionalItems,
            items: $items,
            contains: $contains
        );
    }

    /**
     * @param ?array<int, \OAS\Schema> $properties
     */
    public static function createObjectType(
        ?array $properties = null,
        ?int $minProperties = null,
        ?int $maxProperties = null,
        null|bool|Schema $additionalProperties = null,
        ?array $required = null
    ): self
    {
        return new self(
            maxProperties: $maxProperties,
            minProperties: $minProperties,
            required: $required,
            type: self::TYPE_OBJECT,
            additionalProperties: $additionalProperties,
            properties: $properties
        );
    }

    public function hasExample(): bool
    {
        return !is_null($this->schema()->example);
    }

    public function getExample()
    {
        return $this->schema()->example;
    }

    public function isAlwaysValid(): bool
    {
        return (bool) $this->schema()->alwaysValid;
    }

    public function isAlwaysInvalid(): bool
    {
        return (bool) $this->schema()->alwaysInvalid;
    }

    public function getReference(): ?Schema
    {
        if ($this->hasRef()) {
            return $this->find($this->_ref);
        }

        return null;
    }

    protected function schema(): self
    {
        return $this->isReference() ? $this->getReference() : $this;
    }

    protected function isReference(): bool
    {
        return $this->hasRef();
    }

    public function get(string $path): mixed
    {
        return retrieveByPath($this, pathSegments($path));
    }

    public function offsetExists($offset): bool
    {
        return in_array(
            self::normalizePropertyName($offset), $this->getReflectedProperties()
        );
    }

    public function offsetGet($offset): mixed
    {
        return $this->schema()->{self::normalizePropertyName($offset)};
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

    /**
     * @param array<int, \OAS\Schema> $schemas
     */
    private function setChildren(array $schemas): void
    {
        foreach ($schemas as $schema) {
            $this->__connect($schema);
        }
    }

    public function jsonSerialize(): stdClass|array|bool
    {
        if ($this->isAlwaysValid()) {
            return true;
        }

        if ($this->isAlwaysInvalid()) {
            return false;
        }

        $properties = array_filter(
            get_object_vars($this),
            fn ($value, $property) => !is_null($value) && !str_starts_with($property, '__'),
            ARRAY_FILTER_USE_BOTH
        );

        return empty($properties)
            ? new stdClass()
            : self::denormalizePropertyNames($properties);
    }
}
