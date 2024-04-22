<?php declare(strict_types=1);

namespace OAS\Schema\Factory;

use OAS\Schema;
use OAS\Schema\NullValue;
use OAS\Schema\Type;
use OAS\Utils\ConstructorParametersResolver\Event\BeforeParamsResolution;
use OAS\Utils\ConstructorParametersResolver\Event\BeforeParamValueResolution;
use OAS\Utils\ConstructorParametersResolver\Event\BeforeValueResolution;
use OAS\Utils\ConstructorParametersResolver\SubscriberInterface;
// TODO: require implicitly phpDocumentator!
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Object_;
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
                [$this, 'constructSchemaIfMapProvided'],
                [$this, 'castZeroDecimalFloatToInteger'],
                [$this, 'convertStringToEnumType']
            ],
            BeforeValueResolution::class => [
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
            $parameters['const'] = new NullValue();
        }

        if (array_key_exists('default', $parameters) && is_null($parameters['default'])) {
            $parameters['default'] = new NullValue();
        }

        $event->setParams($parameters);
    }

    public function castStdClassToArray(BeforeValueResolution $event): void
    {
        $rawValue = $event->getRawValue();

        if ($rawValue instanceof stdClass) {
            $event->setRawValue((array) $rawValue);
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

    public function castZeroDecimalFloatToInteger(BeforeParamValueResolution $event): void
    {
        $propertyNames = [
            'maxLength',
            'minLength',
            'maxItems',
            'minItems',
            'maxProperties',
            'minProperties',
            'maxContains',
            'minContains'
        ];
        $value = $event->getRawValue();

        if (in_array($event->name, $propertyNames) && is_float($value) && $value == intval($value)) {
            $event->setValue(intval($value));
        }
    }

    // TODO implement it for code generator!
    public function convertStringToEnumType(BeforeParamValueResolution $event): void
    {
        if ($event->name === 'type') {
            $rawValue = $event->getRawValue();

            if (is_string($rawValue)) {
                $event->setValue(Type::from($rawValue));
            }

            if (is_array($rawValue)) {
                $event->setValue(
                    array_map(fn (string $rawValue) => Type::from($rawValue), $rawValue)
                );
            }
        }
    }
}
