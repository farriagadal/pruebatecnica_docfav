<?php

namespace App\Domain\User\Repository;

use App\Domain\User\User;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\UserEmail;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findById(UserId $id): ?User;
    public function findByEmail(UserEmail $email): ?User;
    public function delete(UserId $id): void;
} 