<?php

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\User\ValueObject\UserName;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class UserNameType extends StringType
{
    public const NAME = 'user_name';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof UserName ? $value->value() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? UserName::fromString($value) : null;
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