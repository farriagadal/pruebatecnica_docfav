<?php

namespace App\Domain\User\ValueObject;

use App\Domain\User\Exception\InvalidEmailException;

final class UserEmail
{
    private string $value;

    private function __construct(string $email)
    {
        $this->ensureIsValidEmail($email);
        $this->value = $email;
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    public function value(): string
    {
        return $this->value;
    }

    private function ensureIsValidEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($email);
        }
    }

    // Method to allow automatic conversion to string
    public function __toString(): string
    {
        return $this->value;
    }
} 