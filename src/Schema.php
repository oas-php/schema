<?php declare(strict_types=1);

namespace OAS;

use OAS\Resolver\Resolver;
use Biera\{ArrayConstructor, ArrayAccessor};
use function iter\all;
use function iter\func\operator;
use function Biera\retrieveByPath;
use function Biera\pathSegments;

/**
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.1.0.md#schemaObject
 */
class Schema implements \JsonSerializable, \ArrayAccess
{
    use ArrayConstructor, ArrayAccessor;

    public static ?Resolver $resolver = null;
    public static bool $resolve = true;

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

    private ?string $title;

    private ?string $description;

    /** @var array|string|null */
    private $type;

    /** @var ?string[] */
    private ?array $enum;

    /** @var Schema\ConstNull|mixed  */
    private $const;

    private ?int $minLength;

    private ?int $maxLength;

    private ?string $format;

    private ?string $pattern;

    /** @var int|float|null */
    private $multipleOf;

    /** @var int|float|null */
    private $minimum;

    /** @var int|float|null */
    private $exclusiveMinimum;

    /** @var int|float|null */
    private $maximum;

    /** @var int|float|null */
    private $exclusiveMaximum;

    /** @var ?Schema[] */
    private ?array $properties;

    /** @var ?Schema[] */
    private ?array $patternProperties;

    private ?Schema $propertyNames;

    private ?int $minProperties;

    private ?int $maxProperties;

    private ?Schema $additionalProperties;

    /** @var ?string[]  */
    private ?array $required;

    /** @var Schema[]|Schema|null  */
    private $items;

    private ?Schema $additionalItems;

    private ?int $minItems;

    private ?int $maxItems;

    private ?bool $uniqueItems;

    private ?Schema $contains;

    private ?int $minContains;

    private ?int $maxContains;

    /** @var ?Schema[] */
    private ?array $allOf;

    /** @var ?Schema[] */
    private ?array $oneOf;

    /** @var ?Schema[] */
    private ?array $anyOf;

    private ?Schema $not;

    private ?Schema $if;

    private ?Schema $then;

    private ?Schema $else;

    /** @var ?Schema[] */
    private ?array $dependentSchemas;

    /** @var mixed */
    private $example;

    private ?bool $deprecated;

    /** @var ?Schema[] */
    private ?array $_defs;

    private ?string $_id;

    private ?string $_ref;

    private ?Schema $__parent = null;

    private ?bool $alwaysValid = null;

    private ?bool $alwaysInvalid = null;

    /**
     * @param string|null                       $title
     * @param string|null                       $description
     * @param string[]|string|null              $type
     * @param array|null                        $enum
     * @param                                   $const
     * @param int|null                          $minLength
     * @param int|null                          $maxLength
     * @param string|null                       $format
     * @param string|null                       $pattern
     * @param int|float|null                    $multipleOf
     * @param int|float|null                    $minimum
     * @param int|float|null                    $exclusiveMinimum
     * @param int|float|null                    $maximum
     * @param int|float|null                    $exclusiveMaximum
     * @param \OAS\Schema[]|null                $properties
     * @param \OAS\Schema[]|null                $patternProperties
     * @param \OAS\Schema|null                  $propertyNames
     * @param int|null                          $minProperties
     * @param int|null                          $maxProperties
     * @param \OAS\Schema|null                  $additionalProperties
     * @param string[]|null                     $required
     * @param \OAS\Schema[]|\OAS\Schema|null    $items
     * @param \OAS\Schema|null                  $additionalItems
     * @param int|null                          $minItems
     * @param int|null                          $maxItems
     * @param bool|null                         $uniqueItems
     * @param \OAS\Schema|null                  $contains
     * @param int|null                          $minContains
     * @param int|null                          $maxContains
     * @param \OAS\Schema[]|null                $allOf
     * @param \OAS\Schema[]|null                $oneOf
     * @param \OAS\Schema[]|null                $anyOf
     * @param \OAS\Schema|null                  $not
     * @param \OAS\Schema|null                  $if
     * @param \OAS\Schema|null                  $then
     * @param \OAS\Schema|null                  $else
     * @param \OAS\Schema[]|null                $dependentSchemas
     * @param                                   $example
     * @param bool|null                         $deprecated
     * @param \OAS\Schema[]|null                $_defs
     * @param string|null                       $_id
     * @param string|null                       $_ref
     */
    public function __construct(
        // annotations
        string $title = null,
        string $description = null,
        $type = null,
        array $enum = null,
        $const = null,

        // string related parameters
        int $minLength = null,
        int $maxLength = null,
        string $format = null,
        string $pattern = null,

        // number related properties
        $multipleOf = null,
        $minimum = null,
        $exclusiveMinimum = null,
        $maximum = null,
        $exclusiveMaximum = null,

        // object type related properties
        array $properties = null,
        array $patternProperties = null,
        Schema $propertyNames = null,
        int $minProperties = null,
        int $maxProperties = null,
        Schema $additionalProperties = null,
        array $required = null,

        // array related properties
        $items = null,
        Schema $additionalItems = null,
        int $minItems = null,
        int $maxItems = null,
        bool $uniqueItems = null,
        Schema $contains = null,
        int $minContains = null,
        int $maxContains = null,

        // composite schemas
        array $allOf = null,
        array $oneOf = null,
        array $anyOf = null,
        Schema $not = null,

        Schema $if = null,
        Schema $then = null,
        Schema $else = null,
        array $dependentSchemas = null,

        $example = null,
        bool $deprecated = null,
        array $_defs = null,
        string $_id = null,
        string $_ref = null
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->setType($type);
        $this->enum = $enum;
        $this->const = $const;

        // validation keywords for strings
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        $this->format = $format;
        $this->pattern = $pattern;

        // validation keywords for numeric instances  (number and integer)
        $this->setMultipleOf($multipleOf);
        $this->setMinimum($minimum);
        $this->setExclusiveMinimum($exclusiveMinimum);
        $this->setMaximum($maximum);
        $this->setExclusiveMaximum($exclusiveMaximum);

        // validation keywords for objects
        $this->setProperties($properties);
        $this->setPatternProperties($patternProperties);
        $this->propertyNames = $propertyNames;
        $this->minProperties = $minProperties;
        $this->maxProperties = $maxProperties;
        $this->setAdditionalProperties($additionalProperties);
        $this->setRequired($required);

        // validation keywords for arrays
        $this->setItems($items);
        $this->setAdditionalItems($additionalItems);
        $this->minItems = $minItems;
        $this->maxItems = $maxItems;
        $this->uniqueItems = $uniqueItems;
        $this->setContains($contains);
        $this->minContains = $minContains;
        $this->maxContains = $maxContains;

        $this->setAllOf($allOf);
        $this->setAnyOf($anyOf);
        $this->setOneOf($oneOf);
        $this->setNot($not);

        $this->setIf($if);
        $this->setThen($then);
        $this->setElse($else);
        $this->setDependentSchemas($dependentSchemas);

        $this->example = $example;
        $this->deprecated = $deprecated;

        $this->setDefs($_defs);
        $this->_id = $_id;
        $this->_ref = $_ref;
    }

    public static function createFromArray(array $params): self
    {
        if (\array_key_exists('const', $params) && \is_null($params['const'])) {
            $params['const'] = new Schema\ConstNull;
        }

        return self::doCreateFromArray($params);
    }

    public static function getResolver(): Resolver
    {
        if (\is_null(self::$resolver)) {
            self::$resolver = new Resolver();
        }

        return self::$resolver;
    }

    /**
     * @param \OAS\Resolver\Graph\Node|array $params
     * @param array $metadata
     * @return \OAS\Schema
     */
    public static function createFromPrimitives($params, array $metadata = []): self
    {
        if (\is_bool($params)) {
            return $params
                ? self::createAlwaysValidSchema() : self::createAlwaysInvalidSchema();
        }

        // for \json_decode($json, false) outputs
        if ($params instanceof \stdClass) {
            $params = (array) $params;
        }

        if (self::$resolve && !($metadata['resolved'] ?? false)) {
            $params = self::getResolver()
                ->resolveDecoded($params)
                ->denormalize(true);

            $metadata['resolved'] = true;
        }

        if (!\is_array($params)) {
            throw new \TypeError(
                'Parameter "params" must be of bool|array|\stdClass type'
            );
        }

        return self::doCreateFromPrimitives(
            self::normalizePropertyNames($params), $metadata
        );
    }

    public static function createAlwaysValidSchema(): self
    {
        $schema = new self();
        $schema->alwaysValid = true;

        return $schema;
    }

    public static function createAlwaysInvalidSchema(): self
    {
        $schema = new self();
        $schema->alwaysInvalid = true;

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

    public function hasTitle(): bool
    {
        return !is_null($this->schema()->title);
    }

    public function getTitle(): ?string
    {
        return $this->schema()->title;
    }

    public function hasDescription(): bool
    {
        return !is_null($this->schema()->description);
    }

    public function getDescription(): ?string
    {
        return $this->schema()->description;
    }

    private function setType($type): void
    {
        $types = $type;

        if (!is_null($types)) {
            if (is_string($types)) {
                $types = [$types];
            }

            if (!is_array($types) && !all(fn($type) => is_string($type), $types)) {
                throw new \TypeError(
                    'Parameter "type" must be of string|string[]|null type',
                );
            }

            $allowedTypes = self::TYPES;

            if (!all(fn($type) => in_array($type, $allowedTypes), $types)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Parameter "type" value must be one of: %s ("%s" provided)',
                        join(', ', self::TYPES),
                        join(', ', $types)
                    )
                );
            }
        }

        $this->type = $type;
    }

    public function hasType(): bool
    {
        return !is_null($this->schema()->type);
    }

    /**
     * @return string[]|string|null
     */
    public function getType()
    {
        return $this->schema()->type;
    }

    public function hasEnum(): bool
    {
        return !is_null($this->schema()->enum);
    }

    public function getEnum(): ?array
    {
        return $this->schema()->enum;
    }

    public function hasConst(): bool
    {
        return !is_null($this->schema()->const);
    }

    public function getConst()
    {
        $const = $this->schema()->const;

        return  $const instanceof Schema\ConstNull ? null : $const;
    }

    public function hasMinLength(): bool
    {
        return !is_null($this->schema()->minLength);
    }

    public function getMinLength(): ?int
    {
        return $this->schema()->minLength;
    }

    public function hasMaxLength(): bool
    {
        return !is_null($this->schema()->maxLength);
    }

    public function getMaxLength(): ?int
    {
        return $this->schema()->maxLength;
    }

    public function hasFormat(): bool
    {
        return !is_null($this->schema()->format);
    }

    public function getFormat(): ?string
    {
        return $this->schema()->format;
    }

    public function hasPattern(): bool
    {
        return !is_null($this->schema()->pattern);
    }

    public function getPattern(): ?string
    {
        return $this->schema()->pattern;
    }

    private function setMultipleOf($multipleOf): void
    {
        if (!is_null($multipleOf) && !(is_int($multipleOf) || is_float($multipleOf))) {
            throw new \TypeError(
                'Parameter "multipleOf" must be of int|float|null type',
            );
        }

        $this->multipleOf = $multipleOf;
    }

    public function hasMultipleOf(): bool
    {
        return !is_null($this->schema()->multipleOf);
    }

    /**
     * @return float|int|null
     */
    public function getMultipleOf()
    {
        return $this->schema()->multipleOf;
    }

    private function setMinimum($minimum): void
    {
        if (!is_null($minimum) && !(is_int($minimum) || is_float($minimum))) {
            throw new \TypeError(
                'Parameter "minimum" must be of int|float|null type',
            );
        }

        $this->minimum = $minimum;
    }

    public function hasMinimum(): bool
    {
        return !is_null($this->schema()->minimum);
    }

    /**
     * @return float|int|null
     */
    public function getMinimum()
    {
        return $this->schema()->minimum;
    }

    private function setExclusiveMinimum($exclusiveMinimum): void
    {
        if (!is_null($exclusiveMinimum) && !(is_int($exclusiveMinimum) || is_float($exclusiveMinimum))) {
            throw new \TypeError(
                'Parameter "exclusiveMinimum" must be of int|float|null type',
            );
        }

        $this->exclusiveMinimum = $exclusiveMinimum;
    }

    public function hasExclusiveMinimum(): bool
    {
        return !is_null($this->schema()->exclusiveMinimum);
    }

    /**
     * @return float|int|null
     */
    public function getExclusiveMinimum()
    {
        return $this->schema()->exclusiveMinimum;
    }

    private function setMaximum($maximum): void
    {
        if (!is_null($maximum) && !(is_int($maximum) || is_float($maximum))) {
            throw new \TypeError(
                'Parameter "maximum" must be of int|float|null type',
            );
        }

        $this->maximum = $maximum;
    }

    public function hasMaximum(): bool
    {
        return !is_null($this->schema()->maximum);
    }

    /**
     * @return int|float|null
     */
    public function getMaximum()
    {
        return $this->schema()->maximum;
    }

    private function setExclusiveMaximum($exclusiveMaximum): void
    {
        if (!is_null($exclusiveMaximum) && !(is_int($exclusiveMaximum) || is_float($exclusiveMaximum))) {
            throw new \TypeError(
                'Parameter "exclusiveMaximum" must be of int|float|null type',
            );
        }

        $this->exclusiveMaximum = $exclusiveMaximum;
    }

    public function hasExclusiveMaximum(): bool
    {
        return !is_null($this->schema()->exclusiveMaximum);
    }

    /**
     * @return float|int|null
     */
    public function getExclusiveMaximum()
    {
        return $this->schema()->exclusiveMaximum;
    }

    private function setProperties(?array $properties): void
    {
        if (!all(operator('instanceof', Schema::class), $properties ?? [])) {
            throw new \TypeError(
                'Parameter "properties" must be of ?\OAS\Schema[] type'
            );
        }

        $this->setChild($properties ?? []);
        $this->properties = $properties;
    }

    public function hasProperties(): bool
    {
        return !is_null($this->schema()->properties);
    }

    /**
     * @return \OAS\Schema[]|null
     */
    public function getProperties(): ?array
    {
        return $this->schema()->properties;
    }

    private function setPatternProperties(?array $patternProperties): void
    {
        if (!all(operator('instanceof', Schema::class), $patternProperties ?? [])) {
            throw new \TypeError(
                'Parameter "patternProperties" must be of ?\OAS\Schema[] type'
            );
        }

        $this->setChild($patternProperties ?? []);
        $this->patternProperties = $patternProperties;
    }

    public function hasPatternProperties(): bool
    {
        return !is_null($this->schema()->patternProperties);
    }

    /**
     * @return \OAS\Schema[]|null
     */
    public function getPatternProperties(): ?array
    {
        return $this->schema()->patternProperties;
    }

    public function hasPropertyNames(): bool
    {
        return !is_null($this->schema()->propertyNames);
    }

    public function getPropertyNames(): ?Schema
    {
        return $this->schema()->propertyNames;
    }

    public function hasMinProperties(): bool
    {
        return !is_null($this->schema()->minProperties);
    }

    public function getMinProperties(): ?int
    {
        return $this->schema()->minProperties;
    }

    public function hasMaxProperties(): bool
    {
        return !is_null($this->schema()->maxProperties);
    }

    public function getMaxProperties(): ?int
    {
        return $this->schema()->maxProperties;
    }

    private function setAdditionalProperties(?Schema $additionalProperties): void
    {
        if (!is_null($additionalProperties)) {
            $this->setChild([$additionalProperties]);
        }

        $this->additionalProperties = $additionalProperties;
    }

    public function hasAdditionalProperties(): bool
    {
        return !is_null($this->schema()->additionalProperties);
    }

    /**
     * @return \OAS\Schema|bool|null
     */
    public function getAdditionalProperties()
    {
        return $this->schema()->additionalProperties;
    }

    private function setRequired(?array $required): void
    {
        if (!all('is_string', $required ?? [])) {
            throw new \TypeError(
                'Parameter "required" must be of ?string[] type',
            );
        }

        $this->required = $required;
    }

    public function hasRequired(): bool
    {
        return !is_null($this->schema()->required);
    }

    public function getRequired(): ?array
    {
        return $this->schema()->required;
    }

    private function setItems($items): void
    {
        if (!is_null($items)) {
            $isSchema = $items instanceof Schema;

            if (!$isSchema && (!is_array($items) || !all(operator('instanceof', Schema::class), $items))) {
                throw new \TypeError(
                    'Parameter "items" must be of ?\OAS\Schema[]|\OAS\Schema type'
                );
            }

            $this->setChild($isSchema ? [$items] : $items);
        }

        $this->items = $items;
    }

    public function hasItems(): bool
    {
        return !is_null($this->schema()->items);
    }


    public function isTuple(): bool
    {
        return $this->hasItems() && is_array($this->getItems());
    }

    /**
     * @return Schema[]|Schema|null
     */
    public function getItems()
    {
        return $this->schema()->items;
    }

    private function setAdditionalItems(?Schema $additionalItems): void
    {
        if (!is_null($additionalItems)) {
            $this->setChild([$additionalItems]);
        }

        $this->additionalItems = $additionalItems;
    }

    public function hasAdditionalItems()
    {
        return !is_null($this->schema()->additionalItems);
    }

    public function getAdditionalItems(): ?Schema
    {
        return $this->schema()->additionalItems;
    }

    public function hasMinItems(): bool
    {
        return !is_null($this->schema()->minItems);
    }

    public function getMinItems(): ?int
    {
        return $this->schema()->minItems;
    }

    public function hasMaxItems(): bool
    {
        return !is_null($this->schema()->maxItems);
    }

    public function getMaxItems(): ?int
    {
        return $this->schema()->maxItems;
    }

    public function hasUniqueItems(): bool
    {
        return !is_null($this->schema()->uniqueItems);
    }

    public function getUniqueItems(): ?bool
    {
        return $this->schema()->uniqueItems;
    }

    private function setContains(?Schema $contains): void
    {
        if (!is_null($contains)) {
            $this->setChild([$contains]);
        }

        $this->contains = $contains;
    }

    public function hasContains(): bool
    {
        return !is_null($this->schema()->contains);
    }

    public function getContains(): ?Schema
    {
        return $this->schema()->contains;
    }

    public function hasMinContains(): bool
    {
        return !is_null($this->schema()->minContains);
    }

    public function getMinContains(): ?int
    {
        return $this->schema()->minContains;
    }

    public function hasMaxContains(): bool
    {
        return !is_null($this->schema()->maxContains);
    }

    public function getMaxContains(): ?int
    {
        return $this->schema()->maxContains;
    }

    private function setAllOf(?array $allOf): void
    {
        if (!is_null($allOf)) {
            if (!all(operator('instanceof', Schema::class), $allOf)) {
                throw new \TypeError(
                    'Parameter "allOf" must be of ?\OAS\Schema[] type'
                );
            }

            $this->setChild($allOf);
        }

        $this->allOf = $allOf;
    }

    public function hasAllOf(): bool
    {
        return !empty($this->schema()->allOf);
    }

    /**
     * @return \OAS\Schema[]|null
     */
    public function getAllOf(): ?array
    {
        return $this->schema()->allOf;
    }

    private function setOneOf(?array $oneOf): void
    {
        if (!is_null($oneOf)) {
            if (!all(operator('instanceof', Schema::class), $oneOf)) {
                throw new \TypeError(
                    'Parameter "oneOf" must be of ?\OAS\Schema[] type'
                );
            }

            $this->setChild($oneOf);
        }

        $this->oneOf = $oneOf;
    }

    public function hasOneOf(): bool
    {
        return !empty($this->schema()->oneOf);
    }

    /**
     * @return \OAS\Schema[]|null
     */
    public function getOneOf(): ?array
    {
        return $this->schema()->oneOf;
    }

    private function setAnyOf(?array $anyOf): void
    {
        if (!is_null($anyOf)) {
            if (!all(operator('instanceof', Schema::class), $anyOf ?? [])) {
                throw new \TypeError(
                    'Parameter "anyOf" must be of ?\OAS\Schema[] type'
                );
            }

            $this->setChild($anyOf);
        }

        $this->anyOf = $anyOf;
    }

    public function hasAnyOf(): bool
    {
        return !empty($this->schema()->anyOf);
    }

    /**
     * @return \OAS\Schema[]|null
     */
    public function getAnyOf(): ?array
    {
        return $this->schema()->anyOf;
    }

    private function setNot(?Schema $not): void
    {
        if (!is_null($not)) {
            $this->setChild([$not]);
        }

        $this->not = $not;
    }

    public function hasNot(): bool
    {
        return !is_null($this->schema()->not);
    }

    public function getNot(): ?Schema
    {
        return $this->schema()->not;
    }

    private function setIf(?Schema $if): void
    {
        if (!is_null($if)) {
            $this->setChild([$if]);
        }

        $this->if = $if;
    }

    public function hasIf(): bool
    {
        return !is_null($this->schema()->if);
    }

    public function getIf(): ?Schema
    {
        return $this->schema()->if;
    }

    private function setThen(?Schema $then): void
    {
        if (!is_null($then)) {
            $this->setChild([$then]);
        }

        $this->then = $then;
    }

    public function hasThen(): bool
    {
        return !is_null($this->schema()->then);
    }

    public function getThen(): ?Schema
    {
        return $this->schema()->then;
    }

    private function setElse(?Schema $else): void
    {
        if (!is_null($else)) {
            $this->setChild([$else]);
        }

        $this->else = $else;
    }

    public function hasElse(): bool
    {
        return !is_null($this->schema()->else);
    }

    public function getElse(): ?Schema
    {
        return $this->schema()->else;
    }

    private function setDependentSchemas(?array $dependentSchemas): void
    {
        if (!all(operator('instanceof', Schema::class), $dependentSchemas ?? [])) {
            throw new \TypeError(
                'Parameter "dependentSchemas" must be of ?\OAS\Schema[] type'
            );
        }

        $this->setChild($dependentSchemas ?? []);
        $this->dependentSchemas = $dependentSchemas;
    }

    public function hasDependentSchemas(): bool
    {
        return !is_null($this->schema()->dependentSchemas);
    }

    /**
     * @return \OAS\Schema[]|null
     */
    public function getDependentSchemas(): ?array
    {
        return $this->schema()->dependentSchemas;
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

    public function hasDeprecated(): bool
    {
        return !is_null($this->schema()->deprecated);
    }

    public function isDeprecated(): ?bool
    {
        return $this->schema()->deprecated;
    }

    public function getDeprecated(): ?bool
    {
        return $this->schema()->deprecated;
    }

    private function setDefs(?array $_defs): void
    {
        if (!is_null($_defs)) {
            if (!all(operator('instanceof', Schema::class), $_defs)) {
                throw new \TypeError(
                    'Parameter "_defs" must be of ?\OAS\Schema[] type'
                );
            }

            $this->setChild($_defs);
        }

        $this->_defs = $_defs;
    }

    public function hasDefs(): bool
    {
        return !is_null($this->schema()->_defs);
    }

    /**
     * @return Schema[]|null
     */
    public function getDefs(): ?array
    {
        return $this->schema()->_defs;
    }

    public function hasId(): bool
    {
        return !is_null($this->schema()->_id);
    }

    public function getId(): ?string
    {
        return $this->schema()->_id;
    }

    public function hasRef(): bool
    {
        return !is_null($this->_ref);
    }

    public function getRef(): ?string
    {
        return $this->_ref;
    }

    public function getReference(): ?Schema
    {
        if ($this->hasRef()) {
            $path = '#' === $this->_ref[0]
                ? substr($this->_ref, 1)
                : $this->_ref;

            return $this->root($this)->get($path);
        }

        return null;
    }

    protected function schema(): self
    {
        return $this->hasRef() ? $this->getReference() : $this;
    }

    private function root(Schema $node): Schema
    {
        return is_null($node->__parent) ? $node : $this->root($node->__parent);
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

    public function offsetExists($offset)
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

    private static function normalizePropertyNames(array $properties): array
    {
        return array_combine(
            array_map(
                [__CLASS__, 'normalizePropertyName'],
                array_keys($properties)
            ),
            $properties
        );
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
    private function setChild(array $schemas): void
    {
        foreach ($schemas as $schema) {
            $schema->__parent = $this;
        }
    }

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
