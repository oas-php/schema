<?php declare(strict_types=1);

namespace OAS\Biera;

class Dispatcher
{
    private array $subscribers = [];

    public function subscribe(string $event, callable $subscriber): void
    {
        if (!array_key_exists($event, $this->subscribers)) {
            $this->subscribers[$event] = [];
        }

        $this->subscribers[$event][] = $subscriber;
    }

    public function dispatch(object $event): void
    {
        $eventName = get_class($event);

        foreach ($this->subscribers[$eventName] ?? [] as $subscriber) {
            call_user_func($subscriber, $event);
        }
    }
}
