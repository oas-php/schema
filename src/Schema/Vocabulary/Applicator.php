<?php declare(strict_types=1);

namespace OAS\Schema\Vocabulary;

use OAS\Schema;
use TypeError;
use function iter\all;
use function iter\func\operator;
use function OAS\Utils\assertTypeValid;

trait Applicator
{
    private ?Schema $additionalItems = null;
    // TODO
    private ?Schema $unevaluatedItems;
    /** @var null|Schema|array<int, Schema> $items  */
    private null|Schema|array $items = null;
    private ?Schema $contains = null;
    private ?Schema $additionalProperties = null;
    private ?Schema $unevaluatedProperties;
    /** @var ?array<string, Schema> $properties */
    private ?array $properties;
    /** @var ?array<string, Schema> $patternProperties */
    private ?array $patternProperties;
    /** @var ?array<string, Schema> $dependentSchemas */
    private ?array $dependentSchemas;
    private ?Schema $propertyNames;
    private ?Schema $if;
    private ?Schema $then;
    private ?Schema $else;
    /** @var ?array<int, \OAS\Schema> $allOf*/
    private ?array $allOf = null;
    /** @var ?array<int, \OAS\Schema> $anyOf */
    private ?array $anyOf = null;
    /** @var ?array<int, \OAS\Schema> $oneOf */
    private ?array $oneOf = null;
    private ?Schema $not;

    private function setAdditionalItems(Schema $additionalItems): void
    {
        $this->setChildren([$additionalItems]);
        $this->additionalItems = $additionalItems;
    }

    public function hasAdditionalItems(): bool
    {
        return !is_null($this->schema()->additionalItems);
    }

    public function getAdditionalItems(): ?Schema
    {
        return $this->schema()->additionalItems;
    }

    /**
     * @param \OAS\Schema|array<int, \OAS\Schema> $items
     */
    private function setItems(Schema|array $items = null): void
    {
        assertTypeValid('\OAS\Schema|array<int, \OAS\Schema>', $items, 'items');

        $this->setChildren($items instanceof Schema ? [$items] : $items);
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
     * @return array<int, Schema>|Schema|null
     */
    public function getItems(): array|Schema|null
    {
        return $this->schema()->items;
    }

    private function setContains(Schema $contains): void
    {
        $this->setChildren([$contains]);
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

    private function setAdditionalProperties(Schema $additionalProperties): void
    {
        $this->setChildren([$additionalProperties]);
        $this->additionalProperties = $additionalProperties;
    }

    public function hasAdditionalProperties(): bool
    {
        return !is_null($this->schema()->additionalProperties);
    }

    public function getAdditionalProperties(): Schema|bool|null
    {
        return $this->schema()->additionalProperties;
    }

    /**
     * TODO: validate keys (type)
     *
     * @param ?array<string, \OAS\Schema> $properties
     */
    private function setProperties(?array $properties): void
    {
        if (!all(operator('instanceof', Schema::class), $properties ?? [])) {
            throw new TypeError('Parameter "properties" must be of ?\OAS\Schema[] type');
        }

        $this->setChildren($properties ?? []);
        $this->properties = $properties;
    }

    public function hasProperties(): bool
    {
        return !is_null($this->schema()->properties);
    }

    /**
     * @return ?array<string, \OAS\Schema>
     */
    public function getProperties(): ?array
    {
        return $this->schema()->properties;
    }

    /**
     * TODO: validate keys (type)
     *
     * @param ?array<string, \OAS\Schema> $patternProperties
     */
    private function setPatternProperties(?array $patternProperties): void
    {
        if (!all(operator('instanceof', Schema::class), $patternProperties ?? [])) {
            throw new TypeError('Parameter "patternProperties" must be of ?\OAS\Schema[] type');
        }

        $this->setChildren($patternProperties ?? []);
        $this->patternProperties = $patternProperties;
    }

    public function hasPatternProperties(): bool
    {
        return !is_null($this->schema()->patternProperties);
    }

    /**
     * @return ?<string, Schema>
     */
    public function getPatternProperties(): ?array
    {
        return $this->schema()->patternProperties;
    }

    /**
     * @param ?array<string, \OAS\Schema> $dependentSchemas
     */
    private function setDependentSchemas(?array $dependentSchemas): void
    {
        if (!all(operator('instanceof', Schema::class), $dependentSchemas ?? [])) {
            throw new TypeError('Parameter "dependentSchemas" must be of ?\OAS\Schema[] type');
        }

        $this->setChildren($dependentSchemas ?? []);
        $this->dependentSchemas = $dependentSchemas;
    }

    public function hasDependentSchemas(): bool
    {
        return !is_null($this->schema()->dependentSchemas);
    }

    /**
     * @return ?<string, Schema>
     */
    public function getDependentSchemas(): ?array
    {
        return $this->schema()->dependentSchemas;
    }

    public function hasPropertyNames(): bool
    {
        return !is_null($this->schema()->propertyNames);
    }

    public function getPropertyNames(): ?Schema
    {
        return $this->schema()->propertyNames;
    }

    private function setIf(?Schema $if): void
    {
        if (!is_null($if)) {
            $this->setChildren([$if]);
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
            $this->setChildren([$then]);
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
            $this->setChildren([$else]);
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

    /**
     * @param array<int, \OAS\Schema> $allOf
     */
    private function setAllOf(array $allOf): void
    {
        assertTypeValid('array<int, \OAS\Schema>', $allOf, 'allOf');

        $this->setChildren($allOf);
        $this->allOf = $allOf;
    }

    public function hasAllOf(): bool
    {
        return !empty($this->schema()->allOf);
    }

    /**
     * @return ?array<int, Schema>
     */
    public function getAllOf(): ?array
    {
        return $this->schema()->allOf;
    }

    /**
     * @param array<int, \OAS\Schema> $anyOf
     */
    private function setAnyOf(?array $anyOf): void
    {
        assertTypeValid('array<int, \OAS\Schema>', $anyOf, 'anyOf');

        $this->setChildren($anyOf);
        $this->anyOf = $anyOf;
    }

    public function hasAnyOf(): bool
    {
        return !empty($this->schema()->anyOf);
    }

    /**
     * @return ?array<int, \OAS\Schema>
     */
    public function getAnyOf(): ?array
    {
        return $this->schema()->anyOf;
    }

    /**
     * @param array<int, \OAS\Schema> $oneOf
     */
    private function setOneOf(array $oneOf): void
    {
        assertTypeValid('array<int, \OAS\Schema>', $oneOf, 'oneOf');

        $this->setChildren($oneOf);
        $this->oneOf = $oneOf;
    }

    public function hasOneOf(): bool
    {
        return !empty($this->schema()->oneOf);
    }

    /**
     * @return ?array<int, Schema>
     */
    public function getOneOf(): ?array
    {
        return $this->schema()->oneOf;
    }

    private function setNot(?Schema $not): void
    {
        if (!is_null($not)) {
            $this->setChildren([$not]);
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

    private function isListOf(mixed $value, string $type): bool
    {
        return is_array($value) && all('is_int', array_keys($value))
            && all(operator('instanceof', $type), $value);
    }
}
