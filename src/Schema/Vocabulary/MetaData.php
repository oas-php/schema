<?php declare(strict_types=1);

namespace OAS\Schema\Vocabulary;

trait MetaData
{
    private ?string $title = null;
    private ?string $description = null;
    private mixed $default = null;
    private ?bool $deprecated = null;
    private ?bool $readOnly = null;
    private ?bool $writeOnly = null;
    private ?array $examples = null;

    public function hasTitle(): bool
    {
        return !is_null($this->title);
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function hasDescription(): bool
    {
        return !is_null($this->description);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function hasDeprecated(): bool
    {
        return !is_null($this->deprecated);
    }

    public function getDeprecated(): ?bool
    {
        return $this->deprecated;
    }

    public function isDeprecated(): bool
    {
        return $this->hasDeprecated() ? $this->deprecated : false;
    }

    public function hasDefault(): bool
    {
        return !is_null($this->default);
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function hasReadOnly(): bool
    {
        return !is_null($this->readOnly);
    }

    public function getReadOnly(): ?bool
    {
        return $this->readOnly;
    }

    public function isReadOnly(): bool
    {
        return $this->hasReadOnly() ? $this->readOnly : false;
    }

    public function hasWriteOnly(): bool
    {
        return !is_null($this->writeOnly);
    }

    public function getWriteOnly(): ?bool
    {
        return $this->writeOnly;
    }

    public function isWriteOnly(): bool
    {
        return $this->hasWriteOnly() ? $this->writeOnly : false;
    }

    public function hasExamples(): bool
    {
        return !is_null($this->examples);
    }

    public function getExamples(): ?array
    {
        return $this->examples;
    }
}
