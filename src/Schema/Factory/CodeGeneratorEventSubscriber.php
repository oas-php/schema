<?php declare(strict_types=1);

namespace OAS\Schema\Factory;

use OAS\Schema;
use OAS\Schema\ConstNull;
use OAS\Utils\ConstructorParametersResolver\Event\AfterParamsResolution;
use OAS\Utils\ConstructorParametersResolver\Event\BeforeParamsResolution;
use OAS\Utils\ConstructorParametersResolver\Event\BeforeParamValueResolution;
use OAS\Utils\ConstructorParametersResolver\Event\BeforeValueResolution;
use PhpParser\BuilderFactory;

class CodeGeneratorEventSubscriber extends ConstructorEventSubscriber
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
            ],
            AfterParamsResolution::class => [
                [$this, 'trimDefaultValues']
            ]
        ];
    }

    public function detectConstNull(BeforeParamsResolution $event): void
    {
        $parameters = $event->getParams();

        if (array_key_exists('const', $parameters) && is_null($parameters['const'])) {
            $parameters['const'] = (new BuilderFactory)->new(ConstNull::class);
            $event->setParams($parameters);
        }
    }

    public function detectBooleanSchema(BeforeValueResolution $event): void
    {
        $value = $event->getRawValue();

        if (is_bool($value)) {
            $event->setValue(
                (new BuilderFactory)->staticCall(Schema::class, 'createBooleanSchema', [$value])
            );
        }
    }

    // TODO: generate code with named parameters (skipping parameters with default values)
    public function trimDefaultValues(AfterParamsResolution $event): void
    {
        $params = $event->getParams();
        $lastNotNullParameter = 0;

        foreach (array_values(array_reverse($params)) as $parameterValue) {
            if (!is_null($parameterValue)) {
                break;
            }

            $lastNotNullParameter++;
        }

        $event->setParams(
            array_slice($params, 0, count($params) - $lastNotNullParameter)
        );
    }
}
