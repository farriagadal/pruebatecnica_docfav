<?php

namespace App\Domain\User\Exception;

use InvalidArgumentException;

final class WeakPasswordException extends InvalidArgumentException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
} 