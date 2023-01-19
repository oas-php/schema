<?php declare(strict_types=1);

namespace OAS\Schema\Factory;

use OAS\Schema;
use OAS\Schema\ConstNull;
use OAS\Utils\ConstructorParametersResolver\Event\AfterParamsResolution;
use OAS\Utils\ConstructorParametersResolver\Event\BeforeParamsResolution;
use OAS\Utils\ConstructorParametersResolver\Event\BeforeParamValueResolution;
use OAS\Utils\ConstructorParametersResolver\Event\BeforeValueResolution;
use OAS\Utils\ConstructorParametersResolver\SubscriberInterface;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Object_;
use PhpParser\BuilderFactory;
use stdClass;
use function iter\all;

class ConstructorEventSubscriber implements SubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            BeforeParamsResolution::class => [
                [$this, 'detectConstNull'],
                [$this, 'renameParametersName']
            ],
            BeforeParamValueResolution::class => [
                [$this, 'castStdClassToArray'],
                [$this, 'constructSchemaIfMapProvided']
            ],
            BeforeValueResolution::class => [
                [$this, 'detectBooleanSchema'],
                [$this, 'castStdClassToArray']
            ]
        ];
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

    public function detectConstNull(BeforeParamsResolution $event): void
    {
        $parameters = $event->getParams();

        if (array_key_exists('const', $parameters) && is_null($parameters['const'])) {
            $parameters['const'] = new ConstNull();
            $event->setParams($parameters);
        }
    }

    public function castStdClassToArray(BeforeValueResolution $event): void
    {
        $rawValue = $event->getRawValue();

        if ($rawValue instanceof stdClass) {
            $event->setRawValue((array) $rawValue);
        }
    }

    public function detectBooleanSchema(BeforeValueResolution $event): void
    {
        $value = $event->getRawValue();

        if (is_bool($value)) {
            $event->setValue(Schema::createBooleanSchema($value));
        }
    }

    /**
     * The "items" param of \OAS\Schema constructor has \OAS\Schema[]|\OAS\Schema type
     * Constructor tries to instantiate objects according to declared types in order they appear in the code.
     * This listener detects the desired type by checking a type of provided value:
     *  -> if type is a map (assoc array or stdClass object): the type is \OAS\Schema
     *  -> otherwise it must be \OAS\Schema[]
     */
    public function constructSchemaIfMapProvided(BeforeParamValueResolution $event): void
    {
        if ($event->name !== 'items') {
            return;
        }

        $value = $event->getRawValue();

        if ($value instanceof stdClass || (is_array($value) && all('is_string', array_keys($value)))) {
            $event->setType(
                new Object_(
                    new Fqsen('\\'.Schema::class)
                )
            );
        }
    }
}
