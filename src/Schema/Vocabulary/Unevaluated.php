<?php declare(strict_types=1);

namespace OAS\Schema\Vocabulary;

use OAS\Schema;

trait Unevaluated
{
    private Schema|bool|null $unevaluatedProperties = null;
    private Schema|bool|null $unevaluatedItems = null;

    public function setUnevaluatedProperties(Schema|bool $unevaluatedProperties): void
    {
        $this->setChild($unevaluatedProperties, 'unevaluatedProperties');
        $this->unevaluatedProperties = $unevaluatedProperties;
    }

    public function hasUnevaluatedProperties(): bool
    {
        return !is_null($this->unevaluatedProperties);
    }

    public function getUnevaluatedProperties(): Schema|bool|null
    {
        return $this->unevaluatedProperties;
    }

    public function setUnevaluatedItems(Schema|bool $unevaluatedItems): void
    {
        $this->setChild($unevaluatedItems, 'unevaluatedItems');
        $this->unevaluatedItems = $unevaluatedItems;
    }

    public function hasUnevaluatedItems(): bool
    {
        return !is_null($this->unevaluatedItems);
    }

    public function getUnevaluatedItems(): Schema|bool|null
    {
        return $this->unevaluatedItems;
    }
}