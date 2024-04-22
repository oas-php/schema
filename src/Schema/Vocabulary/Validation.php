<?php declare(strict_types=1);

namespace OAS\Schema\Vocabulary;

use InvalidArgumentException;
use OAS\Schema\NullValue;
use OAS\Schema\Type;
use function OAS\Utils\assertTypeValid;

trait Validation
{
    /** @var \OAS\Schema\Type|array<int, \OAS\Schema\Type> $type */
    private Type|array|null $type = null;
    /** @var ?array<int, mixed> $enum */
    private ?array $enum = null;
    private mixed $const = null;
    private int|float|null $multipleOf = null;
    private int|float|null $maximum = null;
    private int|float|null $exclusiveMaximum = null;
    private int|float|null $minimum = null;
    private int|float|null $exclusiveMinimum = null;
    private ?int $maxLength = null;
    private ?int $minLength = null;
    private ?string $pattern = null;
    private ?int $maxItems = null;
    private ?int $minItems = null;
    private ?bool $uniqueItems = null;
    private ?int $maxContains = null;
    private ?int $minContains = null;
    private ?int $maxProperties = null;
    private ?int $minProperties = null;
    /** @var ?array<int, string> $required*/
    private ?array $required = null;
    /** @var ?array<string, array<int, string>> $dependentRequired */
    private ?array $dependentRequired = null;

    /**
     * @param \OAS\Schema\Type|array<int, \OAS\Schema\Type> $type
     */
    private function setType(array|Type $type): void
    {
        assertTypeValid('\OAS\Schema\Type|array<int, \OAS\Schema\Type>|null', $type,'type');
        $this->type = $type;
    }

    public function hasType(): bool
    {
        return !is_null($this->type);
    }

    /**
     * @return \OAS\Schema\Type|array<int, \OAS\Schema\Type>|null
     */
    public function getType(): Type|array|null
    {
        return $this->type;
    }

    /**
     * @param array<int, mixed> $enum
     * @return void
     */
    private function setEnum(array $enum): void
    {
        assertTypeValid('array<int, mixed>', $enum, 'enum');

        $this->enum = $enum;
    }

    public function hasEnum(): bool
    {
        return !is_null($this->enum);
    }

    public function getEnum(): ?array
    {
        return $this->enum;
    }

    public function hasConst(): bool
    {
        return !is_null($this->const);
    }

    public function getConst(): mixed
    {
        $const = $this->const;

        return $const instanceof NullValue ? null : $const;
    }

    private function setMultipleOf(int|float $multipleOf): void
    {
        if ($multipleOf <= 0) {
            throw new InvalidArgumentException(
                'The value of "multipleOf" parameter must be positive'
            );
        }

        $this->multipleOf = $multipleOf;
    }

    public function hasMultipleOf(): bool
    {
        return !is_null($this->multipleOf);
    }

    public function getMultipleOf(): null|int|float
    {
        return $this->multipleOf;
    }

    public function hasMaximum(): bool
    {
        return !is_null($this->maximum);
    }

    public function getMaximum(): null|int|float
    {
        return $this->maximum;
    }

    public function hasExclusiveMaximum(): bool
    {
        return !is_null($this->exclusiveMaximum);
    }

    public function getExclusiveMaximum(): null|int|float
    {
        return $this->exclusiveMaximum;
    }

    public function hasMinimum(): bool
    {
        return !is_null($this->minimum);
    }

    public function getMinimum(): null|int|float
    {
        return $this->minimum;
    }

    public function hasExclusiveMinimum(): bool
    {
        return !is_null($this->exclusiveMinimum);
    }

    public function getExclusiveMinimum(): null|int|float
    {
        return $this->exclusiveMinimum;
    }

    private function setMaxLength(int $maxLength): void
    {
        if ($maxLength < 0) {
            throw new InvalidArgumentException(
                'The value of "maxLength" parameter must be a non negative integer'
            );
        }

        $this->maxLength = $maxLength;
    }

    public function hasMaxLength(): bool
    {
        return !is_null($this->maxLength);
    }

    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    private function setMinLength(int $minLength): void
    {
        if ($minLength < 0) {
            throw new InvalidArgumentException(
                'The value of "minLength" parameter must be a non negative integer'
            );
        }

        $this->minLength = $minLength;
    }

    public function hasMinLength(): bool
    {
        return !is_null($this->minLength);
    }

    public function getMinLength(): ?int
    {
        return $this->minLength;
    }

    public function hasPattern(): bool
    {
        return !is_null($this->pattern);
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    private function setMaxItems(int $maxItems): void
    {
        if ($maxItems < 0) {
            throw new InvalidArgumentException(
                'The value of "maxItems" parameter must be a non negative integer'
            );
        }

        $this->maxItems = $maxItems;
    }

    public function hasMaxItems(): bool
    {
        return !is_null($this->maxItems);
    }

    public function getMaxItems(): ?int
    {
        return $this->maxItems;
    }

    private function setMinItems(int $minItems): void
    {
        if ($minItems < 0) {
            throw new InvalidArgumentException(
                'The value of "minItems" parameter must be a non negative integer'
            );
        }

        $this->minItems = $minItems;
    }
    
    public function hasMinItems(): bool
    {
        return !is_null($this->minItems);
    }

    public function getMinItems(): ?int
    {
        return $this->minItems;
    }

    public function hasUniqueItems(): bool
    {
        return !is_null($this->uniqueItems);
    }

    public function getUniqueItems(): ?bool
    {
        return $this->uniqueItems;
    }

    private function setMaxContains(int $maxContains): void
    {
        if ($maxContains < 0) {
            throw new InvalidArgumentException(
                'The value of "maxContains" parameter must be a non negative integer'
            );
        }

        $this->maxContains = $maxContains;
    }

    public function hasMaxContains(): bool
    {
        return !is_null($this->maxContains);
    }

    public function getMaxContains(): ?int
    {
        return $this->maxContains;
    }

    private function setMinContains(int $minContains): void
    {
        if ($minContains < 0) {
            throw new InvalidArgumentException(
                'The value of "minContains" parameter must be a non negative integer'
            );
        }

        $this->minContains = $minContains;
    }

    public function hasMinContains(): bool
    {
        return !is_null($this->minContains);
    }

    public function getMinContains(): ?int
    {
        return $this->minContains;
    }

    private function setMaxProperties(int $maxProperties): void
    {
        if ($maxProperties < 0) {
            throw new InvalidArgumentException(
                'The value of "maxProperties" parameter must be a non negative integer'
            );
        }

        $this->maxProperties = $maxProperties;
    }

    public function hasMaxProperties(): bool
    {
        return !is_null($this->maxProperties);
    }

    public function getMaxProperties(): ?int
    {
        return $this->maxProperties;
    }

    private function setMinProperties(int $minProperties): void
    {
        if ($minProperties < 0) {
            throw new InvalidArgumentException(
                'The value of "minProperties" parameter must be a non negative integer'
            );
        }

        $this->minProperties = $minProperties;
    }

    public function hasMinProperties(): bool
    {
        return !is_null($this->minProperties);
    }

    public function getMinProperties(): ?int
    {
        return $this->minProperties;
    }

    /**
     * @param array<int, string> $required
     */
    private function setRequired(array $required): void
    {
        assertTypeValid('array<int, string>',  $required, 'required');

        $this->required = $required;
    }

    public function hasRequired(): bool
    {
        return !is_null($this->required);
    }

    /**
     * @return ?array<int, string>
     */
    public function getRequired(): ?array
    {
        return $this->required;
    }

    /**
     * @param array<string, array<int, string>> $dependentRequired
     */
    private function setDependentRequired(array $dependentRequired): void
    {
        assertTypeValid('array<string, array<int, string>>', $dependentRequired, 'dependentRequired');

        $this->dependentRequired = $dependentRequired;
    }

    public function hasDependentRequired(): bool
    {
        return !is_null($this->dependentRequired);
    }

    public function getDependentRequired(): ?array
    {
        return $this->dependentRequired;
    }
}
