<?php declare(strict_types=1);

namespace OAS\Schema\Vocabulary;

use OAS\Schema;
use function OAS\Utils\assertTypeValid;

trait Applicator
{
    /** @var ?array<int, \OAS\Schema|bool> $allOf*/
    private ?array $allOf = null;
    /** @var ?array<int, \OAS\Schema|bool> $anyOf */
    private ?array $anyOf = null;
    /** @var ?array<int, \OAS\Schema|bool> $oneOf */
    private ?array $oneOf = null;
    private Schema|bool|null $not = null;
    private Schema|bool|null $if = null;
    private Schema|bool|null $then = null;
    private Schema|bool|null $else = null;
    /** @var ?array<string, \OAS\Schema|bool> $dependentSchemas */
    private ?array $dependentSchemas = null;

    /** @var ?array<int, \OAS\Schema|bool> $prefixItems  */
    private ?array $prefixItems = null;
    private Schema|bool|null $items = null;
    private Schema|bool|null $contains = null;
    /** @var ?array<string, \OAS\Schema|bool> $properties */
    private ?array $properties = null;
    /** @var ?array<string, \OAS\Schema|bool> $patternProperties */
    private ?array $patternProperties = null;
    private Schema|bool|null $additionalProperties = null;
    private Schema|bool|null $propertyNames = null;

    /**
     * @param array<int, \OAS\Schema|bool> $allOf
     */
    private function setAllOf(array $allOf): void
    {
        assertTypeValid('array<int, \OAS\Schema|bool>', $allOf, 'allOf');

        $this->setChildren($allOf, ['allOf']);
        $this->allOf = $allOf;
    }

    public function hasAllOf(): bool
    {
        return !is_null($this->allOf);
    }

    /**
     * @return ?array<int, Schema>
     */
    public function getAllOf(): ?array
    {
        return $this->allOf;
    }

    /**
     * @param array<int, \OAS\Schema|bool> $anyOf
     */
    private function setAnyOf(array $anyOf): void
    {
        assertTypeValid('array<int, \OAS\Schema|bool>', $anyOf, 'anyOf');

        $this->setChildren($anyOf, ['anyOf']);
        $this->anyOf = $anyOf;
    }

    public function hasAnyOf(): bool
    {
        return !is_null($this->anyOf);
    }

    /**
     * @return ?array<int, \OAS\Schema|bool>
     */
    public function getAnyOf(): ?array
    {
        return $this->anyOf;
    }

    /**
     * @param array<int, \OAS\Schema|bool> $oneOf
     */
    private function setOneOf(array $oneOf): void
    {
        assertTypeValid('array<int, \OAS\Schema|bool>', $oneOf, 'oneOf');

        $this->setChildren($oneOf, ['oneOf']);
        $this->oneOf = $oneOf;
    }

    public function hasOneOf(): bool
    {
        return !is_null($this->oneOf);
    }

    /**
     * @return ?array<int, \OAS\Schema>
     */
    public function getOneOf(): ?array
    {
        return $this->oneOf;
    }

    private function setNot(Schema|bool $not): void
    {
        $this->setChild($not, 'not');
        $this->not = $not;
    }

    public function hasNot(): bool
    {
        return !is_null($this->not);
    }

    public function getNot(): Schema|bool|null
    {
        return $this->not;
    }

    private function setIf(Schema|bool $if): void
    {
        $this->setChild($if, 'if');
        $this->if = $if;
    }

    public function hasIf(): bool
    {
        return !is_null($this->if);
    }

    public function getIf(): Schema|bool|null
    {
        return $this->if;
    }

    private function setThen(Schema|bool $then): void
    {
        $this->setChild($then, 'then');
        $this->then = $then;
    }

    public function hasThen(): bool
    {
        return !is_null($this->then);
    }

    public function getThen(): Schema|bool|null
    {
        return $this->then;
    }

    private function setElse(Schema|bool $else): void
    {
        $this->setChild($else, 'else');
        $this->else = $else;
    }

    public function hasElse(): bool
    {
        return !is_null($this->else);
    }

    public function getElse(): Schema|bool|null
    {
        return $this->else;
    }

    /**
     * @param array<string, \OAS\Schema|bool> $dependentSchemas
     */
    private function setDependentSchemas(array $dependentSchemas): void
    {
        assertTypeValid('?array<string, \OAS\Schema|bool>', $dependentSchemas, 'dependentSchemas');

        $this->setChildren($dependentSchemas, ['dependentSchemas']);
        $this->dependentSchemas = $dependentSchemas;
    }

    public function hasDependentSchemas(): bool
    {
        return !is_null($this->dependentSchemas);
    }

    /**
     * @return ?array<string, \OAS\Schema|bool>
     */
    public function getDependentSchemas(): ?array
    {
        return $this->dependentSchemas;
    }

    /** @param array<int, \OAS\Schema|bool> $prefixItems */
    private function setPrefixItems(array $prefixItems): void
    {
        assertTypeValid('?array<int, \OAS\Schema|bool>', $prefixItems, 'prefixItems');

        $this->setChildren($prefixItems, ['prefixItems']);
        $this->prefixItems = $prefixItems;
    }

    public function hasPrefixItems(): bool
    {
        return !is_null($this->prefixItems);
    }

    /**
     * @return ?array<int, \OAS\Schema|bool>
     */
    public function getPrefixItems(): ?array
    {
        return $this->prefixItems;
    }

    private function setItems(Schema|bool $items): void
    {
        $this->setChild($items, 'items');
        $this->items = $items;
    }

    public function hasItems(): bool
    {
        return !is_null($this->items);
    }

    public function getItems(): Schema|bool|null
    {
        return $this->items;
    }

    private function setContains(Schema|bool $contains): void
    {
        $this->setChild($contains, 'contains');
        $this->contains = $contains;
    }

    public function hasContains(): bool
    {
        return !is_null($this->contains);
    }

    public function getContains(): Schema|bool|null
    {
        return $this->contains;
    }

    /**
     * @param array<string, \OAS\Schema|bool> $properties
     */
    private function setProperties(array $properties): void
    {
        assertTypeValid('?array<string, \OAS\Schema|bool>', $properties, 'properties');

        $this->setChildren($properties, ['properties']);
        $this->properties = $properties;
    }

    public function hasProperties(): bool
    {
        return !is_null($this->properties);
    }

    /**
     * @return ?array<string, \OAS\Schema|bool>
     */
    public function getProperties(): ?array
    {
        return $this->properties;
    }

    /**
     * @param array<string, \OAS\Schema|bool> $patternProperties
     */
    private function setPatternProperties(array $patternProperties): void
    {
        assertTypeValid('?array<string, \OAS\Schema|bool>', $patternProperties, 'patternProperties');

        $this->setChildren($patternProperties, ['patternProperties']);
        $this->patternProperties = $patternProperties;
    }

    public function hasPatternProperties(): bool
    {
        return !is_null($this->patternProperties);
    }

    /**
     * @return ?array<string, \OAS\Schema|bool>
     */
    public function getPatternProperties(): ?array
    {
        return $this->patternProperties;
    }

    private function setAdditionalProperties(Schema|bool $additionalProperties): void
    {
        $this->setChild($additionalProperties, 'additionalProperties');
        $this->additionalProperties = $additionalProperties;
    }

    public function hasAdditionalProperties(): bool
    {
        return !is_null($this->additionalProperties);
    }

    public function getAdditionalProperties(): Schema|bool|null
    {
        return $this->additionalProperties;
    }

    public function setPropertyNames(Schema|bool $propertyNames): void
    {
        $this->setChild($propertyNames, 'propertyNames');
        $this->propertyNames = $propertyNames;
    }

    public function hasPropertyNames(): bool
    {
        return !is_null($this->propertyNames);
    }

    public function getPropertyNames(): Schema|bool|null
    {
        return $this->propertyNames;
    }

    public function isTuple(): bool
    {
        return $this->hasPrefixItems() && $this->getItems() === false;
    }
}
