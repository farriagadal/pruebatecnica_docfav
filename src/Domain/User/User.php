<?php

namespace App\Domain\User;

use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\UserName;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserPassword;
use App\Infrastructure\Doctrine\Type\UserIdType;
use App\Infrastructure\Doctrine\Type\UserNameType;
use App\Infrastructure\Doctrine\Type\UserEmailType;
use App\Infrastructure\Doctrine\Type\UserPasswordType;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
final class User
{
    #[ORM\Id]
    #[ORM\Column(type: UserIdType::NAME, length: 36)]
    private UserId $id;

    #[ORM\Column(type: UserNameType::NAME, length: 100)]
    private UserName $name;

    #[ORM\Column(type: UserEmailType::NAME, length: 100, unique: true)]
    private UserEmail $email;

    #[ORM\Column(type: UserPasswordType::NAME, length: 100)]
    private UserPassword $password;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    private function __construct(
        UserId $id,
        UserName $name,
        UserEmail $email,
        UserPassword $password,
        DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
    }

    public static function create(
        UserName $name,
        UserEmail $email,
        UserPassword $password
    ): self {
        return new self(
            UserId::generate(),
            $name,
            $email,
            $password,
            new DateTimeImmutable()
        );
    }

    public static function fromPrimitives(
        string $id,
        string $name,
        string $email,
        string $hashedPassword,
        string $createdAt
    ): self {
        return new self(
            UserId::fromString($id),
            UserName::fromString($name),
            UserEmail::fromString($email),
            UserPassword::fromHash($hashedPassword),
            new DateTimeImmutable($createdAt)
        );
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): UserName
    {
        return $this->name;
    }

    public function email(): UserEmail
    {
        return $this->email;
    }

    public function password(): UserPassword
    {
        return $this->password;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
} 