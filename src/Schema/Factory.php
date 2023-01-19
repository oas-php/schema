<?php declare(strict_types=1);

namespace OAS\Schema;

use OAS\Resolver\Resolver;
use OAS\Schema;
use OAS\Schema\Factory\ConstructorEventSubscriber;
use OAS\Utils\Constructor;
use OAS\Utils\ConstructorParametersResolver;
use OAS\Utils\ConstructorParametersResolver\Dispatcher;
use stdClass;

class Factory
{
    private ?Resolver $resolver;
    private Constructor $constructor;

    public function __construct(Resolver $resolver = null)
    {
        $this->resolver = $resolver;
        $dispatcher = new Dispatcher();
        $dispatcher->subscribe(new ConstructorEventSubscriber());
        $this->constructor = new Constructor(
            new ConstructorParametersResolver($dispatcher)
        );
    }

    /**
     * @param stdClass|array<string, mixed>|bool $primitives
     */
    public function createFromPrimitives(stdClass|array|bool $primitives): Schema
    {
        if (is_bool($primitives)) {
            return Schema::createBooleanSchema($primitives);
        }

        if ($primitives instanceof stdClass) {
            $primitives = (array) $primitives;

            // TODO: is this really necessary?
            // if (empty($primitives)) {
            //     return new Schema();
            // }
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
