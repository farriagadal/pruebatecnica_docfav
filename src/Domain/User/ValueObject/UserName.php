<?php

namespace App\Domain\User\ValueObject;

use InvalidArgumentException;

final class UserName
{
    private string $value;

    private function __construct(string $name)
    {
        $this->ensureIsValidName($name);
        $this->value = $name;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function value(): string
    {
        return $this->value;
    }

    private function ensureIsValidName(string $name): void
    {
        if (strlen($name) < 3) {
            throw new InvalidArgumentException('Name must be at least 3 characters long');
        }

        if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
            throw new InvalidArgumentException('Name can only contain letters and spaces');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
} 