<?php

namespace App\Domain\User\ValueObject;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

final class UserId
{
    private string $value;

    private function __construct(string $value)
    {
        $this->ensureIsValidUuid($value);
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(UserId $otherId): bool
    {
        return $this->value === $otherId->value;
    }

    private function ensureIsValidUuid(string $id): void
    {
        if (!Uuid::isValid($id)) {
            throw new InvalidArgumentException(
                sprintf('<%s> does not allow the value <%s>.', static::class, $id)
            );
        }
    }
    
    // Method to allow automatic conversion to string
    public function __toString(): string
    {
        return $this->value;
    }
} 