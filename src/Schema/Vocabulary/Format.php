<?php declare(strict_types=1);

namespace OAS\Schema\Vocabulary;

trait Format
{
    private ?string $format;

    public function hasFormat(): bool
    {
        return !is_null($this->format);
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }
}
