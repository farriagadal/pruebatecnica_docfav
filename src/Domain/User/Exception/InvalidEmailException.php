<?php

namespace App\Domain\User\Exception;

use InvalidArgumentException;

final class InvalidEmailException extends InvalidArgumentException
{
    public function __construct(string $email)
    {
        parent::__construct(sprintf('The email <%s> is invalid', $email));
    }
} 