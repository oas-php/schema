<?php declare(strict_types=1);

namespace OAS\Schema;

use OAS\Biera\Dispatcher;
use OAS\Biera\Event\BeforeParamResolution;
use OAS\Biera\Event\BeforeParamsResolution;
use OAS\Biera\Event\BeforeParamWithTypeResolution;
use OAS\Resolver\Resolver;
use OAS\Biera\Constructor;
use OAS\Schema;
use function iter\all;

class Factory
{
    private ?Resolver $resolver;
    private Constructor $constructor;

    public function __construct(Resolver $resolver = null)
    {
        $this->resolver = $resolver;
        $dispatcher = new Dispatcher();

        $dispatcher->subscribe(BeforeParamsResolution::class, function (BeforeParamsResolution $event) {
            $parameters = $event->getParams();

            if (array_key_exists('const', $parameters) && is_null($parameters['const'])) {
                $parameters['const'] = new ConstNull();
                $event->setParams($parameters);
            }
        });

        $dispatcher->subscribe(BeforeParamsResolution::class, function (BeforeParamsResolution $event) {
            $parameters = $event->getParams();

            $event->setParams(
                array_combine(
                    array_map(
                        function ($parameterName) {
                            if (!empty($parameterName) && '$' == $parameterName[0]) {
                                $parameterName[0] = '_';
                            }

                            return  $parameterName;
                        },
                        array_keys($parameters)
                    ),
                    $parameters
                )
            );
        });

        $dispatcher->subscribe(BeforeParamResolution::class, function (BeforeParamResolution $event) {
            $value = $event->getValue();

            if ($value instanceof \stdClass) {
                $event->setValue(
                    (array) $value
                );
            }
        });

        $dispatcher->subscribe(BeforeParamResolution::class, function (BeforeParamResolution $event) {
            $metadata = $event->getMetadata();

            if (!$metadata->isType('\OAS\Schema') || !$metadata->isType('\OAS\Schema', true)) {
                return;
            }

            $value = $event->getValue();

            if ($value instanceof \stdClass || (is_array($value) && all('is_string', array_keys($value)))) {
                $constructor = $event->getConstructor();

                $event->setValue(
                    $constructor->construct(
                        Schema::class,
                        (array) $value
                    )
                );
            }
        });

        $dispatcher->subscribe(BeforeParamWithTypeResolution::class, function (BeforeParamWithTypeResolution $event) {
            if ('\OAS\Schema' != $event->getType()) {
                return;
            }

            $value = $event->getOriginalValue();

            if (is_bool($value)) {
                $event->setValue(
                    Schema::createBooleanSchema($value)
                );
            }

            if ($value instanceof \stdClass) {
                $value = (array) $value;

                $event->setValue(
                    empty($value) ? new Schema() : $value
                );
            }
        });

        $this->constructor = new Constructor($dispatcher);
    }

    /**
     * @param \stdClass|array|bool $primitives
     * @return Schema|object
     */
    public function createFromPrimitives($primitives): Schema
    {
        if (is_bool($primitives)) {
            return Schema::createBooleanSchema($primitives);
        }

        if ($primitives instanceof \stdClass) {
            $primitives = (array) $primitives;

            if (empty($primitives)) {
                return new Schema();
            }
        }

        if (!is_array($primitives)) {
            throw new \TypeError(
                'Parameter "params" must be of bool|array|\stdClass type'
            );
        }

        return $this->constructor->construct(
            Schema::class,
                $this->resolver
                        // TODO: check if it can be resolved to \stdClass
                        ? $this->resolver
                            ->resolveDecoded($primitives)
                            ->denormalize(true)
                        : $primitives
        );
    }
}
