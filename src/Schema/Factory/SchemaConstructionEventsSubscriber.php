<?php declare(strict_types=1);

namespace OAS\Schema\Factory;

use OAS\Utils\Constructor\Event\AfterParamsResolution;
use OAS\Utils\Constructor\Event\BeforeParamResolution;
use OAS\Utils\Constructor\Event\BeforeParamsResolution;
use OAS\Utils\Constructor\Event\BeforeParamWithTypeResolution;
use OAS\Utils\Constructor\SubscriberInterface;
use OAS\Schema;
use OAS\Schema\ConstNull;
use PhpParser\BuilderFactory;
use function iter\all;

class SchemaConstructionEventsSubscriber implements SubscriberInterface
{
    private bool $construct;

    public function __construct(bool $construct = true)
    {
        $this->construct = $construct;
    }

    function getSubscribedEvents(): array
    {
        return [
            BeforeParamsResolution::class => [
                [$this, 'detectConstNull'],
                [$this, 'renameParametersName']
            ],
            BeforeParamResolution::class => [
                [$this, 'castStdClassToArray'],
                [$this, 'constructSchemaIfMapProvided']
            ],
            BeforeParamWithTypeResolution::class => [
                [$this, 'detectBooleanSchema'],
                [$this, 'castStdClassToArray']
            ],
            AfterParamsResolution::class => [
                [$this, 'trimDefaultValues']
            ]
        ];
    }

    public function castStdClassToArray(BeforeParamResolution $event): void
    {
        $value = $event->getValue();

        if ($value instanceof \stdClass) {
            $event->setValue((array) $value);
        }
    }

    public function detectConstNull(BeforeParamsResolution $event): void
    {
        $parameters = $event->getParams();

        if (array_key_exists('const', $parameters) && is_null($parameters['const'])) {
            $parameters['const'] = $this->construct
                ? new ConstNull() : (new BuilderFactory)->new(ConstNull::class);

            $event->setParams($parameters);
        }
    }

    public function detectBooleanSchema(BeforeParamWithTypeResolution $event): void
    {
        $value = $event->getOriginalValue();

        if (is_bool($value)) {
            $event->setValue(
                $this->construct
                    ? Schema::createBooleanSchema($value)
                    : (new BuilderFactory)->staticCall(Schema::class, 'createBooleanSchema', [$value])
            );
        }
    }

    /**
     * Rename all parameters prefixed with "$" so "$" is replaced by "_"
     *
     * @param BeforeParamsResolution $event
     */
    public function renameParametersName(BeforeParamsResolution $event): void
    {
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
    }

    /**
     * The "items" param of \OAS\Schema constructor has \OAS\Schema[]|\OAS\Schema type
     * Constructor tries to instantiate objects according to declared types in order they appear in the code.
     * This listener detects the desired type by checking a type of provided value:
     *  -> if type is a map (assoc array or stdClass object): the type is \OAS\Schema
     *  -> otherwise it must be \OAS\Schema[] (if not an error is thrown by Constructor)
     *
     * @param BeforeParamResolution $event
     */
    public function constructSchemaIfMapProvided(BeforeParamResolution $event): void
    {
        $metadata = $event->getMetadata();

        if (!$metadata->isType('\OAS\Schema') || !$metadata->isType('\OAS\Schema', true)) {
            return;
        }

        $value = $event->getValue();

        if ($value instanceof \stdClass || (is_array($value) && all('is_string', array_keys($value)))) {
            $constructor = $event->getConstructor();

            $event->setValue(
                $this->construct
                    ? $constructor->construct(Schema::class, (array) $value)
                    : $constructor->getAST(Schema::class, (array) $value)
            );
        }
    }

    public function trimDefaultValues(AfterParamsResolution $event): void
    {
        if ($this->construct) {
            return;
        }

        $params = $event->getParams();

        if (is_array($params)) {
            $lastNotNullParameter = 0;

            foreach (array_values(array_reverse($params)) as $parameterValue) {
                if (!is_null($parameterValue)) {
                    break;
                }

                $lastNotNullParameter++;
            }

            // $lastNotNullParameter => 0 ... count($params)-1

            // $lastNotNullParameter = 0 => slice(0, count($params))
            // $lastNotNullParameter = count($params) - 1 => slice(0, 1)

            $event->setParams(
                array_slice($params, 0, count($params) - ($lastNotNullParameter))
            );
        }
    }
}
