<?php

namespace App\Infrastructure\Event\Listener;

use App\Domain\User\Event\UserRegisteredEvent;

final class WelcomeEmailListener
{
    public function __invoke(UserRegisteredEvent $event): void
    {
        error_log("WelcomeEmailListener: Event received");
        // Here we would simulate sending a welcome email
        $user = $event->user();
        error_log(sprintf(
            "WelcomeEmailListener: Sending welcome email to %s (%s)",
            $user->name()->value(),
            $user->email()->value()
        ));
        error_log("WelcomeEmailListener: Email sent successfully");
    }
}