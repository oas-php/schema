<?php declare(strict_types=1);

namespace OAS\Biera\Event;

use OAS\Biera\Constructor;
use OAS\Biera\ParameterMetadata;

class BeforeParamWithTypeResolution extends BeforeParamResolution
{
    private string $type;

    public function __construct(Constructor $constructor, ParameterMetadata $reflection, $value, string $type)
    {
        $this->type = $type;
        parent::__construct($constructor, $reflection, $value);

    }

    public function getType(): string
    {
        return $this->type;
    }
}
