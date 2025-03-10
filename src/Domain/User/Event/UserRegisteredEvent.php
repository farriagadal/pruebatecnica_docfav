<?php

namespace App\Domain\User\Event;

use App\Domain\User\User;

final class UserRegisteredEvent
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function user(): User
    {
        return $this->user;
    }
} 