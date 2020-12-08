<?php declare(strict_types=1);

namespace OAS\Schema\Vocabulary;

use OAS\Schema;
use function iter\all;
use function iter\func\operator;

trait Applicator
{
    private ?Schema $additionalItems;

    // TODO
    private ?Schema $unevaluatedItems;

    /** @var Schema[]|Schema|null  */
    private $items;

    private ?Schema $contains;

    private ?Schema $additionalProperties;

    private ?Schema $unevaluatedProperties;

    /** @var ?Schema[] */
    private ?array $properties;

    /** @var ?Schema[] */
    private ?array $patternProperties;

    /** @var ?Schema[] */
    private ?array $dependentSchemas;

    private ?Schema $propertyNames;

    private ?Schema $if;

    private ?Schema $then;

    private ?Schema $else;

    /** @var ?Schema[] */
    private ?array $allOf;

    /** @var ?Schema[] */
    private ?array $anyOf;

    /** @var ?Schema[] */
    private ?array $oneOf;

    private ?Schema $not;

    private function setAdditionalItems(?Schema $additionalItems): void
    {
        if (!is_null($additionalItems)) {
            $this->setChildren([$additionalItems]);
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

    private function setItems($items): void
    {
        if (!is_null($items)) {
            $isSchema = $items instanceof Schema;

            if (!$isSchema && (!is_array($items) || !all(operator('instanceof', Schema::class), $items))) {
                throw new \TypeError(
                    'Parameter "items" must be of ?\OAS\Schema[]|\OAS\Schema type'
                );
            }

            $this->setChildren($isSchema ? [$items] : $items);
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

    private function setContains(?Schema $contains): void
    {
        if (!is_null($contains)) {
            $this->setChildren([$contains]);
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

    private function setAdditionalProperties(?Schema $additionalProperties): void
    {
        if (!is_null($additionalProperties)) {
            $this->setChildren([$additionalProperties]);
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

    private function setProperties(?array $properties): void
    {
        if (!all(operator('instanceof', Schema::class), $properties ?? [])) {
            throw new \TypeError(
                'Parameter "properties" must be of ?\OAS\Schema[] type'
            );
        }

        $this->setChildren($properties ?? []);
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

        $this->setChildren($patternProperties ?? []);
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

    private function setDependentSchemas(?array $dependentSchemas): void
    {
        if (!all(operator('instanceof', Schema::class), $dependentSchemas ?? [])) {
            throw new \TypeError(
                'Parameter "dependentSchemas" must be of ?\OAS\Schema[] type'
            );
        }

        $this->setChildren($dependentSchemas ?? []);
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

    private function setAllOf(?array $allOf): void
    {
        if (!is_null($allOf)) {
            if (!all(operator('instanceof', Schema::class), $allOf)) {
                throw new \TypeError(
                    'Parameter "allOf" must be of ?\OAS\Schema[] type'
                );
            }

            $this->setChildren($allOf);
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

    private function setAnyOf(?array $anyOf): void
    {
        if (!is_null($anyOf)) {
            if (!all(operator('instanceof', Schema::class), $anyOf ?? [])) {
                throw new \TypeError(
                    'Parameter "anyOf" must be of ?\OAS\Schema[] type'
                );
            }

            $this->setChildren($anyOf);
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

    private function setOneOf(?array $oneOf): void
    {
        if (!is_null($oneOf)) {
            if (!all(operator('instanceof', Schema::class), $oneOf)) {
                throw new \TypeError(
                    'Parameter "oneOf" must be of ?\OAS\Schema[] type'
                );
            }

            $this->setChildren($oneOf);
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
}
