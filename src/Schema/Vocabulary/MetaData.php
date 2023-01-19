<?php declare(strict_types=1);

namespace OAS\Schema\Vocabulary;

trait MetaData
{
    private ?string $title;
    private ?string $description;
    private mixed $default;
    private ?bool $deprecated;
    private ?bool $readOnly;
    private ?bool $writeOnly;
    private ?array $examples;

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

    public function hasDeprecated(): bool
    {
        return !is_null($this->schema()->deprecated);
    }

    public function getDeprecated(): ?bool
    {
        return $this->schema()->deprecated;
    }

    public function isDeprecated(): bool
    {
        $default = true;

        return $this->hasDeprecated() ? $this->getDeprecated() : $default;
    }

    public function hasDefault(): bool
    {
        return !is_null($this->schema()->default);
    }

    public function getDefault(): mixed
    {
        return $this->schema()->default;
    }

    public function hasReadOnly(): bool
    {
        return !is_null($this->schema()->readOnly);
    }

    public function getReadOnly(): ?bool
    {
        return $this->schema()->readOnly;
    }

    public function isReadOnly(): bool
    {
        $default = false;

        return $this->hasReadOnly() ? $this->getReadOnly() : $default;
    }

    public function hasWriteOnly(): bool
    {
        return !is_null($this->schema()->writeOnly);
    }

    public function getWriteOnly(): ?bool
    {
        return $this->schema()->writeOnly;
    }

    public function isWriteOnly(): bool
    {
        $default = false;

        return $this->hasWriteOnly() ? $this->getWriteOnly() : $default;
    }

    public function hasExamples(): bool
    {
        return !is_null($this->schema()->readOnly);
    }

    public function getExamples(): ?array
    {
        return $this->schema()->examples;
    }
}
