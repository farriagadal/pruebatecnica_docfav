<?php

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\User\ValueObject\UserEmail;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class UserEmailType extends StringType
{
    public const NAME = 'user_email';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof UserEmail ? $value->value() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? UserEmail::fromString($value) : null;
    }

    public function getName()
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
} 