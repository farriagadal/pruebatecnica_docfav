<?php

namespace App\Domain\User\ValueObject;

use App\Domain\User\Exception\WeakPasswordException;

final class UserPassword
{
    private string $hashedValue;

    private function __construct(string $hashedPassword)
    {
        $this->hashedValue = $hashedPassword;
    }

    public static function fromPlainPassword(string $plainPassword): self
    {
        self::ensureIsStrongPassword($plainPassword);
        return new self(password_hash($plainPassword, PASSWORD_BCRYPT));
    }

    public static function fromHash(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }

    public function value(): string
    {
        return $this->hashedValue;
    }

    public function verify(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->hashedValue);
    }

    private static function ensureIsStrongPassword(string $password): void
    {
        // At least 8 characters
        if (strlen($password) < 8) {
            throw new WeakPasswordException('Password must be at least 8 characters long');
        }

        // At least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            throw new WeakPasswordException('Password must contain at least one uppercase letter');
        }

        // At least one number
        if (!preg_match('/[0-9]/', $password)) {
            throw new WeakPasswordException('Password must contain at least one number');
        }

        // At least one special character
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            throw new WeakPasswordException('Password must contain at least one special character');
        }
    }

    // Method to allow automatic conversion to string
    public function __toString(): string
    {
        return $this->hashedValue;
    }
} 