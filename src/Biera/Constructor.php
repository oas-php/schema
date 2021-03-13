<?php declare(strict_types=1);

namespace OAS\Biera;

use Biera\InstantiationException;
use Laminas\Code\Reflection\ClassReflection;
use Laminas\Code\Reflection\DocBlock\Tag\ParamTag;
use Laminas\Code\Reflection\ParameterReflection;
use OAS\Biera\Event\AfterParamsResolution;
use OAS\Biera\Event\BeforeParamResolution;
use OAS\Biera\Event\BeforeParamsResolution;
use OAS\Biera\Event\BeforeParamWithTypeResolution;

class Constructor
{
    /**
     * map of shape
     *  class name <string> -> constructor parameters metadata <ParameterMetadata[]>
     */
    private array $parametersMetadata;
    private ?Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
        $this->parametersMetadata = [];
    }

    public function construct(string $type, array $params): object
    {
        // todo: cache this
        $reflection = new ClassReflection($type);

        $beforeResolution = $this->dispatchBeforeParamsEvent($reflection, $params);

        if ($beforeResolution->hasInstance()) {
            return $beforeResolution->getInstance();
        }

        $params = $this->resolveConstructorParameters($reflection, $beforeResolution->getParams());

        $afterResolution = $this->dispatchAfterParamsEvent($reflection, $params);

        if ($afterResolution->hasInstance()) {
            return $afterResolution->getInstance();
        }

        return $reflection->newInstance(
            ...array_values(
                $afterResolution->getParams()
            )
        );
    }

    private function resolveConstructorParameters(ClassReflection $reflection, $parameters): array
    {
        $metadata = $this->getConstructorParametersMetadata($reflection);

        // parameters with at least one non-primitive type, e.g:
        //
        // /**
        //  * @param \GlobIterator|string $dir <-- \GlobIterator is non-primitive
        //  * @param bool $recursively
        //  */
        // function readDir($dir, bool $recursively) {
        //  ...
        // }
        $nonPrimitiveParameters = array_filter(
            $metadata, fn (ParameterMetadata $parameterMetadata) => $parameterMetadata->isComplex()
        );

        $resolvedParameters = array_map(
            function (ParameterMetadata $metadata) use ($parameters) {
                $errors = [];
                $name = $metadata->getName();
                $value = $this->dispatchBeforeParamEvent($metadata, $parameters[$name] ?? null)->getValue();

                if ($metadata->isNullable() && is_null($value)) {
                    return null;
                }

                // iterate over each type (in declaration order)
                // and try to resolve parameter value
                foreach ($metadata->getTypes() as [$type, $isPrimitive, $isList]) {
                    if ($isPrimitive) {
                        return $this->dispatchBeforeParamWithTypeEvent($metadata, $value, $type)->getValue();
                    }

                    try {
                        $classReflection = new ClassReflection($type);

                        if ($isList) {
                            if (is_array($value)) {
                                return array_map(
                                    function ($value) use ($type, $classReflection, $metadata) {
                                        $value = $this->dispatchBeforeParamWithTypeEvent($metadata, $value, $type)->getValue();

                                        if (is_object($value) && $classReflection->isInstance($value)) {
                                            return $value;
                                        }

                                        return $this->construct($type, $value);
                                    },
                                    $value
                                );
                            } else {
                                throw new \RuntimeException("Type {$type}[] expects an array");
                            }
                        } else {
                            $value = $this->dispatchBeforeParamWithTypeEvent($metadata, $value, $type)->getValue();

                            // the value is an instance of declared type
                            if (is_object($value) && $classReflection->isInstance($value)) {
                                return $value;
                            }

                            return $this->construct($type, $value);
                        }

                    } catch (InstantiationException $instantiationError) {
                        throw new InstantiationException($name, [], $instantiationError);
                    } catch (\Throwable $e) {
                        $errors[] = $e;
                    }
                }

                throw new InstantiationException($name, $errors);
            },
            $nonPrimitiveParameters
        );

        $defaults = array_map(
            fn (ParameterMetadata $parameterMetadata) => $parameterMetadata->getDefault(null), $metadata
        );

        return array_merge($defaults, $parameters, $resolvedParameters);
    }

    private function getConstructorParametersMetadata(ClassReflection $reflection): array
    {
        $className = $reflection->getName();

        if (!array_key_exists($className, $this->parametersMetadata)) {
            if ($reflection->hasMethod('__construct')) {
                $constructor = $reflection->getMethod('__construct');
                $dockBlock = $constructor->getDocBlock();
                $paramTags = array_reduce(
                    $dockBlock ? $dockBlock->getTags('param') : [],
                    function ($paramTags, ParamTag $paramTag) {
                        $paramName = $paramTag->getVariableName();

                        if (!is_null($paramName)) {
                            // name is always prefixed with "$"
                            $paramTags[substr($paramName, 1)] = $paramTag;
                        }

                        return $paramTags;
                    },
                    []
                );

                $this->parametersMetadata[$className] =  array_reduce(
                    $constructor->getParameters(),
                    function (array $parametersMetadata, ParameterReflection $reflection) use ($paramTags) {
                        $name = $reflection->getName();
                        $parametersMetadata[$name] = new ParameterMetadata(
                            $reflection, $paramTags[$name] ?? null
                        );

                        return $parametersMetadata;
                    },
                    []
                );
            } else {
                $this->parametersMetadata[$className] = [];
            }
        }

        return $this->parametersMetadata[$className];
    }

    private function dispatchBeforeParamsEvent(ClassReflection $reflection, $params): BeforeParamsResolution
    {
        return $this->conditionallyDispatch(
            new BeforeParamsResolution($reflection, $params)
        );
    }

    private function dispatchAfterParamsEvent(ClassReflection $reflection, $params): AfterParamsResolution
    {
        return $this->conditionallyDispatch(
            new AfterParamsResolution($reflection, $params)
        );
    }

    private function dispatchBeforeParamEvent(ParameterMetadata $metadata, $value): BeforeParamResolution
    {
        return $this->conditionallyDispatch(
            new BeforeParamResolution($this, $metadata, $value)
        );
    }

    private function dispatchBeforeParamWithTypeEvent(ParameterMetadata $metadata, $value, string $type): BeforeParamWithTypeResolution
    {
        return $this->conditionallyDispatch(
            new BeforeParamWithTypeResolution($this, $metadata, $value, $type)
        );
    }

    private function conditionallyDispatch($event): object
    {
        if (!is_null($this->dispatcher)) {
            $this->dispatcher->dispatch($event);
        }

        return $event;
    }
}
