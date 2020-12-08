<?php declare(strict_types=1);

namespace OAS;

use Biera\ArrayAccessor;
use OAS\Schema\Vocabulary;
use OAS\Utils\Node;
use OAS\Utils\Serializable;
use function Biera\retrieveByPath;
use function Biera\pathSegments;

class Schema extends Node implements \JsonSerializable, \ArrayAccess
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

    /** @var mixed */
    private $example;

    private ?bool $alwaysValid = null;

    private ?bool $alwaysInvalid = null;

    /**
     * @param string|null $_id
     * @param string|null $_schema
     * @param string|null $_anchor
     * @param string|null $_ref
     * @param string|null $_recursiveRef
     * @param bool|null $_recursiveAnchor
     * @param array|null $_vocabulary
     * @param string|null $_comment
     * @param \OAS\Schema[]|null $_defs
     * @param string|null $title
     * @param string|null $description
     * @param null $default
     * @param bool|null $deprecated
     * @param bool|null $readOnly
     * @param bool|null $writeOnly
     * @param array|null $examples
     * @param null $example
     * @param string|null $format
     * @param int|float|null $multipleOf
     * @param int|float|null $maximum
     * @param int|float|null $exclusiveMaximum
     * @param int|float|null $minimum
     * @param int|float|null $exclusiveMinimum
     * @param int|null $maxLength
     * @param int|null $minLength
     * @param string|null $pattern
     * @param int|null $minItems
     * @param int|null $maxItems
     * @param bool|null $uniqueItems
     * @param int|null $maxContains
     * @param int|null $minContains
     * @param int|null $maxProperties
     * @param int|null $minProperties
     * @param string[]|null $required
     * @param array|null $dependentRequired
     * @param null $const
     * @param array|null $enum
     * @param string[]|string|null $type
     * @param \OAS\Schema|null $additionalItems
     * @param \OAS\Schema[]|\OAS\Schema|null $items
     * @param \OAS\Schema|null $contains
     * @param \OAS\Schema|null $additionalProperties
     * @param \OAS\Schema[]|null $properties
     * @param \OAS\Schema[]|null $patternProperties
     * @param \OAS\Schema[]|null $dependentSchemas
     * @param \OAS\Schema|null $propertyNames
     * @param \OAS\Schema|null $if
     * @param \OAS\Schema|null $then
     * @param \OAS\Schema|null $else
     * @param \OAS\Schema[]|null $allOf
     * @param \OAS\Schema[]|null $anyOf
     * @param \OAS\Schema[]|null $oneOf
     * @param \OAS\Schema|null $not
     */
    public function __construct(
        // core
        string $_id = null,
        string $_schema = null,
        string $_anchor = null,
        string $_ref = null,
        string $_recursiveRef = null,
        bool $_recursiveAnchor = null,
        array $_vocabulary = null,
        string $_comment = null,
        array $_defs = null,
        // meta
        string $title = null,
        string $description = null,
        $default = null,
        bool $deprecated = null,
        bool $readOnly = null,
        bool $writeOnly = null,
        ?array $examples = null,
        $example = null,
        // format
        string $format = null,
        // validation
        $multipleOf = null,
        $maximum = null,
        $exclusiveMaximum = null,
        $minimum = null,
        $exclusiveMinimum = null,
        int $maxLength = null,
        int $minLength = null,
        string $pattern = null,
        int $minItems = null,
        int $maxItems = null,
        bool $uniqueItems = null,
        int $maxContains = null,
        int $minContains = null,
        int $maxProperties = null,
        int $minProperties = null,
        array $required = null,
        array $dependentRequired = null,
        $const = null,
        array $enum = null,
        $type = null,
        // applicator
        Schema $additionalItems = null,
        $items = null,
        Schema $contains = null,
        Schema $additionalProperties = null,
        array $properties = null,
        array $patternProperties = null,
        array $dependentSchemas = null,
        Schema $propertyNames = null,
        Schema $if = null,
        Schema $then = null,
        Schema $else = null,
        array $allOf = null,
        array $anyOf = null,
        array $oneOf = null,
        Schema $not = null
    ) {
        // core
        $this->_id = $_id;
        $this->_schema = $_schema;
        $this->_anchor = $_anchor;
        $this->_ref = $_ref;
        $this->_recursiveRef = $_recursiveRef;
        $this->_recursiveAnchor = $_recursiveAnchor;
        $this->_vocabulary = $_vocabulary;
        $this->_comment = $_comment;
        $this->setDefs($_defs);

        // metadata
        $this->title = $title;
        $this->description = $description;
        $this->default = $default;
        $this->deprecated = $deprecated;
        $this->readOnly = $readOnly;
        $this->writeOnly = $writeOnly;
        $this->examples = $examples;
        // TODO: to remove?
        $this->example = $example;
        // format
        $this->format = $format;
        // validation
        $this->setMultipleOf($multipleOf);
        $this->setMaximum($maximum);
        $this->setExclusiveMaximum($exclusiveMaximum);
        $this->setMinimum($minimum);
        $this->setExclusiveMinimum($exclusiveMinimum);
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
        $this->setRequired($required);
        $this->setDependentRequired($dependentRequired);
        $this->const = $const;
        $this->enum = $enum;
        $this->setType($type);
        // applicator
        $this->setAdditionalItems($additionalItems);
        $this->setItems($items);
        $this->setContains($contains);
        $this->setAdditionalProperties($additionalProperties);
        $this->setProperties($properties);
        $this->setPatternProperties($patternProperties);
        $this->setDependentSchemas($dependentSchemas);
        $this->propertyNames = $propertyNames;
        $this->setIf($if);
        $this->setThen($then);
        $this->setElse($else);
        $this->setAllOf($allOf);
        $this->setOneOf($oneOf);
        $this->setAnyOf($anyOf);
        $this->setNot($not);
    }

    public static function createFromArray(array $params): self
    {
        if (\array_key_exists('const', $params) && \is_null($params['const'])) {
            $params['const'] = new Schema\ConstNull;
        }

        $constructorParametersMeta =
            (new \ReflectionClass(__CLASS__))
                ->getConstructor()
                ->getParameters();

        $constructorParametersName = array_map(
            fn (\ReflectionParameter $parameter) => $parameter->getName(),
            $constructorParametersMeta
        );

        $defaults = array_combine(
            $constructorParametersName,
            array_map(
                fn (\ReflectionParameter $parameter) => $parameter->getDefaultValue(),
                $constructorParametersMeta
            )
        );

        return new self(
            ...array_values(
                array_merge(
                    $defaults, $params
                )
            )
        );
    }

    public static function createBooleanSchema(bool $value): self
    {
        $schema = new self();
        $schema->{$value ? 'alwaysValid' : 'alwaysInvalid'} = true;

        return $schema;
    }

    public static function createStringType(
        int $minLength = null,
        int $maxLength  = null,
        string $format = null,
        string $pattern = null
    ): self
    {
        return self::createFromArray(
            [
                'type' => Schema::TYPE_STRING,
                'minLength' => $minLength,
                'maxLength' => $maxLength,
                'format' => $format,
                'pattern' => $pattern
            ]
        );
    }

    /**
     * @param int|float|null $multipleOf
     * @param int|float|null $minimum
     * @param int|float|null $exclusiveMinimum
     * @param int|float|null $maximum
     * @param int|float|null $exclusiveMaximum
     * @return \OAS\Schema
     */
    public static function createIntegerType(
        $multipleOf = null,
        $minimum = null,
        $exclusiveMinimum = null,
        $maximum = null,
        $exclusiveMaximum = null
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

    /**
     * @param int|float|null $multipleOf
     * @param int|float|null $minimum
     * @param int|float|null $exclusiveMinimum
     * @param int|float|null $maximum
     * @param int|float|null $exclusiveMaximum
     * @return \OAS\Schema
     */
    public static function createNumberType(
        $multipleOf = null,
        $minimum = null,
        $exclusiveMinimum = null,
        $maximum = null,
        $exclusiveMaximum = null
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
        $multipleOf = null,
        $minimum = null,
        $exclusiveMinimum = null,
        $maximum = null,
        $exclusiveMaximum = null
    ): self
    {
        return self::createFromArray(
            [
                'type' => $type,
                'multipleOf' => $multipleOf,
                'minimum' => $minimum,
                'exclusiveMinimum' => $exclusiveMinimum,
                'maximum' => $maximum,
                'exclusiveMaximum' => $exclusiveMaximum
            ]
        );
    }

    /**
     * @param Schema[]|Schema|null  $items
     * @param Schema|null           $additionalItems
     * @param int|null              $minItems
     * @param int|null              $maxItems
     * @param bool|null             $uniqueItems
     * @param Schema|null           $contains
     * @param int|null              $maxContains
     * @param int|null              $minContains
     * @return Schema
     */
    public static function createArrayType(
        $items = null,
        Schema $additionalItems = null,
        int $minItems = null,
        int $maxItems = null,
        bool $uniqueItems = null,
        Schema $contains = null,
        int $maxContains = null,
        int $minContains = null
    ): self
    {
        $type = self::TYPE_ARRAY;

        return self::createFromArray(
            \compact(
                'type',
                'items',
                'additionalItems',
                'minItems',
                'maxItems',
                'uniqueItems',
                'contains',
                'maxContains',
                'minContains'
            )
        );
    }

    /**
     * @param \OAS\Schema[]|null    $properties
     * @param int|null              $minProperties
     * @param int|null              $maxProperties
     * @param \OAS\Schema|bool|null $additionalProperties
     * @param array|null            $required
     * @return \OAS\Schema
     */
    public static function createObjectType(
        array $properties = null,
        int $minProperties = null,
        int $maxProperties = null,
        $additionalProperties = null,
        array $required = null
    ): self
    {
        return self::createFromArray(
            [
                'type' => self::TYPE_OBJECT,
                'properties' => $properties,
                'minProperties' => $minProperties,
                'maxProperties' => $maxProperties,
                'additionalProperties' => $additionalProperties,
                'required' => $required
            ]
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
        return $this->hasRef() || $this->hasRecursiveRef();
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function get(string $path)
    {
        return retrieveByPath(
            $this, pathSegments($path)
        );
    }

    public function offsetExists($offset): bool
    {
        return in_array(
            self::normalizePropertyName($offset), $this->getReflectedProperties()
        );
    }

    public function offsetGet($offset)
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

                    return  $propertyName;
                },
                array_keys($properties)
            ),
            $properties
        );
    }

    /**
     * @param Schema[] $schemas
     */
    private function setChildren(array $schemas): void
    {
        foreach ($schemas as $schema) {
            $this->__connect($schema);
        }
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        if ($this->isAlwaysValid()) {
            return true;
        }

        if ($this->isAlwaysInvalid()) {
            return false;
        }

        $properties = array_filter(
            get_object_vars($this),
            fn ($value, $property) => !is_null($value) && 0 !== strpos($property, '__'),
            ARRAY_FILTER_USE_BOTH
        );

        return empty($properties)
            ? new \stdClass()
            : self::denormalizePropertyNames($properties);
    }
}
