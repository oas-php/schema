<?php declare(strict_types=1);

namespace OAS\Schema\Factory;

use OAS\Resolver\Resolver;
use OAS\Schema;
use OAS\Utils\Constructor\Dispatcher;
use OAS\Utils\Constructor\Constructor;

class Factory
{
    private ?Resolver $resolver;
    private Constructor $constructor;

    public function __construct(Resolver $resolver = null)
    {
        $this->resolver = $resolver;
        $dispatcher = new Dispatcher();
        $dispatcher->subscribe(
            new SchemaConstructionEventsSubscriber()
        );

        $this->constructor = new Constructor($dispatcher);
    }

    public function getConstructor(): Constructor
    {
        return $this->constructor;
    }

    /**
     * @param \stdClass|array|bool $primitives
     * @return Schema|object
     */
    public function createFromPrimitives($primitives)   // : Schema
    {
        if (is_bool($primitives)) {
            return Schema::createBooleanSchema($primitives);
        }

        if ($primitives instanceof \stdClass) {
            $primitives = (array) $primitives;

            // TODO: is this really necessary?
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
