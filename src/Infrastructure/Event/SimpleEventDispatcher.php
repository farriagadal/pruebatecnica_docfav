<?php

namespace App\Infrastructure\Event;

final class SimpleEventDispatcher implements EventDispatcherInterface
{
    private array $listeners = [];

    public function dispatch(object $event): void
    {
        $eventName = get_class($event);
        error_log("SimpleEventDispatcher: Dispatching event $eventName");
        
        if (!isset($this->listeners[$eventName])) {
            error_log("SimpleEventDispatcher: No listeners registered for $eventName");
            return;
        }
        
        error_log("SimpleEventDispatcher: Found " . count($this->listeners[$eventName]) . " listeners for $eventName");
        foreach ($this->listeners[$eventName] as $listener) {
            error_log("SimpleEventDispatcher: Executing listener " . get_class($listener));
            $listener($event);
        }
        error_log("SimpleEventDispatcher: All listeners executed");
    }

    public function addListener(string $eventName, callable $listener): void
    {
        error_log("SimpleEventDispatcher: Adding listener for $eventName");
        $this->listeners[$eventName][] = $listener;
    }
} 