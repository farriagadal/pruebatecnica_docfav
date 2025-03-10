<?php

namespace Tests\Integration\User;

use App\Application\User\Register\RegisterUserUseCase;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserName;
use App\Domain\User\ValueObject\UserPassword;
use App\Infrastructure\Doctrine\DoctrineUserRepository;
use App\Infrastructure\Event\EventDispatcherInterface;
use App\UI\Http\Controller\RegisterUserController;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class RegisterUserTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private UserRepositoryInterface $userRepository;
    private EventDispatcherInterface $eventDispatcher;
    private RegisterUserUseCase $useCase;
    private RegisterUserController $controller;

    protected function setUp(): void
    {
        // Create mocks for dependencies
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        
        // Create the use case with the repository and event dispatcher
        $this->useCase = new RegisterUserUseCase($this->userRepository, $this->eventDispatcher);
        
        // Create the controller with the use case
        $this->controller = new RegisterUserController($this->useCase);
    }

    public function testSuccessfulUserRegistration(): void
    {
        // Arrange
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'StrongP@ssw0rd'
        ];

        // Configure mocks
        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null); // No existing user found

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class));

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->anything());

        // Act
        $response = $this->controller->__invoke($userData);

        // Assert
        $this->assertEquals('success', $response['status']);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('id', $response['data']);
        $this->assertArrayHasKey('name', $response['data']);
        $this->assertArrayHasKey('email', $response['data']);
        $this->assertArrayHasKey('created_at', $response['data']);
        $this->assertEquals($userData['name'], $response['data']['name']);
        $this->assertEquals($userData['email'], $response['data']['email']);
    }

    public function testUserRegistrationWithExistingEmail(): void
    {
        // Arrange
        $userData = [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'StrongP@ssw0rd'
        ];

        // Create a real User object for the existing user
        $existingUser = User::create(
            UserName::fromString('Existing User'),
            UserEmail::fromString('existing@example.com'),
            UserPassword::fromPlainPassword('StrongP@ssw0rd')
        );

        // Configure mocks to return an existing user
        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($existingUser);

        // The save method should not be called
        $this->userRepository
            ->expects($this->never())
            ->method('save');

        // The event dispatcher should not be called
        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch');

        // Act
        $response = $this->controller->__invoke($userData);

        // Assert
        $this->assertEquals('error', $response['status']);
        $this->assertEquals(409, $response['code']); // Conflict status code
        $this->assertEquals('Email already in use', $response['message']);
    }

    public function testUserRegistrationWithInvalidEmail(): void
    {
        // Arrange
        $userData = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'StrongP@ssw0rd'
        ];

        // Act
        $response = $this->controller->__invoke($userData);

        // Assert
        $this->assertEquals('error', $response['status']);
        $this->assertEquals(400, $response['code']); // Bad request status code
        $this->assertEquals('Invalid email format', $response['message']);
    }

    public function testUserRegistrationWithWeakPassword(): void
    {
        // Arrange
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'weak'
        ];

        // Configure mocks
        $this->userRepository
            ->expects($this->any())
            ->method('findByEmail')
            ->willReturn(null);

        // Act
        $response = $this->controller->__invoke($userData);

        // Assert
        $this->assertEquals('error', $response['status']);
        $this->assertEquals(400, $response['code']); // Bad request status code
        $this->assertStringContainsString('Password', $response['message']);
    }
} 