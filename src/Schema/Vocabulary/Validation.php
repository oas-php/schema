<?php declare(strict_types=1);

namespace OAS\Schema\Vocabulary;

use OAS\Schema\ConstNull;
use function iter\all;

trait Validation
{
    /** @var int|float|null */
    private $multipleOf;

    /** @var int|float|null */
    private $maximum;

    /** @var int|float|null */
    private $exclusiveMaximum;

    /** @var int|float|null */
    private $minimum;

    /** @var int|float|null */
    private $exclusiveMinimum;

    private ?int $maxLength;

    private ?int $minLength;

    private ?string $pattern;

    private ?int $maxItems;

    private ?int $minItems;

    private ?bool $uniqueItems;

    private ?int $maxContains;

    private ?int $minContains;

    private ?int $maxProperties;

    private ?int $minProperties;

    /** @var ?string[]  */
    private ?array $required;

    /**
     *  map <string> => <string[]>
     *
     * @var ?string[]
     */
    private $dependentRequired;

    /** @var \OAS\Schema\ConstNull|mixed  */
    private $const;

    /** @var ?string[] */
    private ?array $enum;

    /** @var array|string|null */
    private $type;

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

    public function hasMaxLength(): bool
    {
        return !is_null($this->schema()->maxLength);
    }

    public function getMaxLength(): ?int
    {
        return $this->schema()->maxLength;
    }

    public function hasMinLength(): bool
    {
        return !is_null($this->schema()->minLength);
    }

    public function getMinLength(): ?int
    {
        return $this->schema()->minLength;
    }

    public function hasPattern(): bool
    {
        return !is_null($this->schema()->pattern);
    }

    public function getPattern(): ?string
    {
        return $this->schema()->pattern;
    }

    public function hasMaxItems(): bool
    {
        return !is_null($this->schema()->maxItems);
    }

    public function getMaxItems(): ?int
    {
        return $this->schema()->maxItems;
    }

    public function hasMinItems(): bool
    {
        return !is_null($this->schema()->minItems);
    }

    public function getMinItems(): ?int
    {
        return $this->schema()->minItems;
    }

    public function hasUniqueItems(): bool
    {
        return !is_null($this->schema()->uniqueItems);
    }

    public function getUniqueItems(): ?bool
    {
        return $this->schema()->uniqueItems;
    }

    public function hasMaxContains(): bool
    {
        return !is_null($this->schema()->maxContains);
    }

    public function getMaxContains(): ?int
    {
        return $this->schema()->maxContains;
    }

    public function hasMinContains(): bool
    {
        return !is_null($this->schema()->minContains);
    }

    public function getMinContains(): ?int
    {
        return $this->schema()->minContains;
    }

    public function hasMaxProperties(): bool
    {
        return !is_null($this->schema()->maxProperties);
    }

    public function getMaxProperties(): ?int
    {
        return $this->schema()->maxProperties;
    }

    public function hasMinProperties(): bool
    {
        return !is_null($this->schema()->minProperties);
    }

    public function getMinProperties(): ?int
    {
        return $this->schema()->minProperties;
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

    private function setDependentRequired(?array $dependentRequired): void
    {
        if (!is_null($dependentRequired)) {
            $isMapOfStringLists = fn ($list) =>
                is_array($list) && all('is_string', $list);

            // checks if $dependentRequired has the following type:
            // [
            //   key <string> => list <string[]>
            // ]
            if (!all('is_string', array_keys($dependentRequired)) || !all($isMapOfStringLists, $dependentRequired)) {
                throw new \TypeError(
                    'Parameter "dependentRequired" must be of ?string[][] type',
                );
            }
        }

        $this->dependentRequired = $dependentRequired;
    }

    public function hasDependentRequired(): bool
    {
        return !is_null($this->schema()->dependentRequired);
    }

    public function getDependentRequired(): ?array
    {
        return $this->schema()->dependentRequired;
    }

    public function hasConst(): bool
    {
        return !is_null($this->schema()->const);
    }

    public function getConst()
    {
        $const = $this->schema()->const;

        return $const instanceof ConstNull ? null : $const;
    }

    public function hasEnum(): bool
    {
        return !is_null($this->schema()->enum);
    }

    public function getEnum(): ?array
    {
        return $this->schema()->enum;
    }

    public function hasType(): bool
    {
        return !is_null($this->schema()->type);
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

    /**
     * @return string[]|string|null
     */
    public function getType()
    {
        return $this->schema()->type;
    }
}
