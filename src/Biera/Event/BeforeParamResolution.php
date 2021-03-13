<?php declare(strict_types=1);

namespace OAS\Biera\Event;

use OAS\Biera\Constructor;
use OAS\Biera\ParameterMetadata;

class BeforeParamResolution extends Event
{
    private ParameterMetadata $reflection;
    private $originalValue;
    private $value;

    public function __construct(Constructor $constructor, ParameterMetadata $reflection, $value)
    {
        $this->reflection = $reflection;
        $this->originalValue = $value;
        parent::__construct($constructor);
    }

    public function getMetadata(): ParameterMetadata
    {
        return $this->reflection;
    }

    public function getOriginalValue()
    {
        return $this->originalValue;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return is_null($this->value) ? $this->originalValue : $this->value;
    }
}
