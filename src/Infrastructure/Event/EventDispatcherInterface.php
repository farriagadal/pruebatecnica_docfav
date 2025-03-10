<?php

namespace App\Infrastructure\Event;

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
    public function addListener(string $eventName, callable $listener): void;
} 