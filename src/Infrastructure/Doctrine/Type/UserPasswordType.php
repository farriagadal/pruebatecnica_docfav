<?php

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\User\ValueObject\UserPassword;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class UserPasswordType extends StringType
{
    public const NAME = 'user_password';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof UserPassword ? $value->value() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? UserPassword::fromHash($value) : null;
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