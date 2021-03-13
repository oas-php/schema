<?php declare(strict_types=1);

namespace OAS\Biera\Event;

use Laminas\Code\Reflection\ClassReflection;

abstract class ParamsResolution
{
    private ClassReflection $reflection;
    private $originalParams;
    private $params = null;
    private $instance = null;

    public function __construct(ClassReflection $reflection, $params)
    {
        $this->reflection = $reflection;
        $this->originalParams = $params;
    }

    public function getReflection(): ClassReflection
    {
        return $this->reflection;
    }

    public function setInstance($instance): void
    {
        $this->instance = $instance;
    }

    public function hasInstance(): bool
    {
        return !is_null($this->instance);
    }

    public function getInstance(): object
    {
        return $this->instance;
    }

    public function getOriginalParams()
    {
        return $this->originalParams;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function getParams()
    {
        return is_null($this->params) ? $this->originalParams : $this->params;
    }
}
