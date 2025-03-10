<?php

namespace App\Domain\User\Exception;

use DomainException;

final class UserAlreadyExistsException extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct(sprintf('The user with email <%s> already exists', $email));
    }
} 