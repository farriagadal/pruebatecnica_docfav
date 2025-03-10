<?php

namespace App\Infrastructure\Doctrine;

use App\Domain\User\User;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserRepository implements UserRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(User $user): void
    {
        error_log("DoctrineUserRepository: Saving user...");
        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            error_log("DoctrineUserRepository: User saved successfully");
        } catch (\Exception $e) {
            error_log("DoctrineUserRepository: Error saving user: " . $e->getMessage());
            error_log("DoctrineUserRepository: Error trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function findById(UserId $id): ?User
    {
        error_log("DoctrineUserRepository: Finding user by ID: " . $id->value());
        return $this->entityManager->find(User::class, $id->value());
    }

    public function findByEmail(UserEmail $email): ?User
    {
        error_log("DoctrineUserRepository: Finding user by email: " . $email->value());
        try {
            $result = $this->entityManager->createQueryBuilder()
                ->select('u')
                ->from(User::class, 'u')
                ->where('u.email = :email')
                ->setParameter('email', $email->__toString())
                ->getQuery()
                ->getOneOrNullResult();
            
            error_log("DoctrineUserRepository: User " . ($result ? "found" : "not found"));
            return $result;
        } catch (\Exception $e) {
            error_log("DoctrineUserRepository: Error finding user by email: " . $e->getMessage());
            error_log("DoctrineUserRepository: Error trace: " . $e->getTraceAsString());
            return null;
        }
    }

    public function delete(UserId $id): void
    {
        error_log("DoctrineUserRepository: Deleting user with ID: " . $id->value());
        $user = $this->findById($id);
        
        if ($user !== null) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            error_log("DoctrineUserRepository: User deleted successfully");
        } else {
            error_log("DoctrineUserRepository: User not found for deletion");
        }
    }
} 