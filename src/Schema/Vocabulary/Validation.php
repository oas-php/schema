<?php declare(strict_types=1);

namespace OAS\Schema\Vocabulary;

use InvalidArgumentException;
use OAS\Schema\ConstNull;
use function iter\all;
use function OAS\Utils\assertTypeValid;

trait Validation
{
    private null|int|float $multipleOf;
    private null|int|float $maximum;
    private null|int|float $exclusiveMaximum;
    private null|int|float $minimum;
    private null|int|float $exclusiveMinimum;
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
    /** @var ?array<int, string> $required*/
    private ?array $required = null;
    /** @var ?array<string, array<int, string>> $dependentRequired */
    private ?array $dependentRequired = null;
    private mixed $const;
    /** @var ?array<int, mixed> $enum */
    private ?array $enum = null;
    /** @var null|string|array<int, string> $type */
    private null|string|array $type;

    public function hasMultipleOf(): bool
    {
        return !is_null($this->schema()->multipleOf);
    }

    public function getMultipleOf(): null|int|float
    {
        return $this->schema()->multipleOf;
    }

    public function hasMaximum(): bool
    {
        return !is_null($this->schema()->maximum);
    }

    public function getMaximum(): null|int|float
    {
        return $this->schema()->maximum;
    }

    public function hasExclusiveMaximum(): bool
    {
        return !is_null($this->schema()->exclusiveMaximum);
    }

    public function getExclusiveMaximum(): null|int|float
    {
        return $this->schema()->exclusiveMaximum;
    }

    public function hasMinimum(): bool
    {
        return !is_null($this->schema()->minimum);
    }

    public function getMinimum(): null|int|float
    {
        return $this->schema()->minimum;
    }

    public function hasExclusiveMinimum(): bool
    {
        return !is_null($this->schema()->exclusiveMinimum);
    }

    public function getExclusiveMinimum(): null|int|float
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
        return !is_null($this->schema()->required);
    }

    /**
     * @return ?array<int, string>
     */
    public function getRequired(): ?array
    {
        return $this->schema()->required;
    }

    private function setDependentRequired(array $dependentRequired): void
    {
        assertTypeValid('array<string, array<int, string>>', $dependentRequired, 'dependentRequired');

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

    public function getConst(): mixed
    {
        $const = $this->schema()->const;

        return $const instanceof ConstNull ? null : $const;
    }

    private function setEnum(array $enum): void
    {
        assertTypeValid('array<int, mixed>', $enum, 'enum');

        $this->enum = $enum;
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

    /**
     * @param string|array<int, string> $type
     */
    private function setType(string|array $type): void
    {
        assertTypeValid('string|array<int, string>', $type,'type');

        $types = is_string($type) ? [$type] : $type;

        if (!all(fn ($type) => in_array($type, self::TYPES), $types)) {
            $wrapApostrophesAround = fn (string $value) => '"'.$value.'"';

            throw new InvalidArgumentException(
                sprintf(
                    'The "type" parameter must have one of the following values: %s (%s provided)',
                    join(', ', array_map($wrapApostrophesAround, self::TYPES)),
                    join(', ', array_map($wrapApostrophesAround, $types))
                )
            );
        }

        $this->type = $type;
    }

    /**
     * @return null|string|array<int, string>
     */
    public function getType(): null|string|array
    {
        return $this->schema()->type;
    }
}
