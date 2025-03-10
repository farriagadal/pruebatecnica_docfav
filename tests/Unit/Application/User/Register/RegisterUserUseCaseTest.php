<?php

namespace Tests\Unit\Application\User\Register;

use App\Application\User\Register\RegisterUserRequest;
use App\Application\User\Register\RegisterUserUseCase;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Exception\UserAlreadyExistsException;
use App\Domain\User\User;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserName;
use App\Domain\User\ValueObject\UserPassword;
use App\Infrastructure\Event\EventDispatcherInterface;
use PHPUnit\Framework\TestCase;

class RegisterUserUseCaseTest extends TestCase
{
    private UserRepositoryInterface $userRepository;
    private EventDispatcherInterface $eventDispatcher;
    private RegisterUserUseCase $useCase;
    
    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->useCase = new RegisterUserUseCase($this->userRepository, $this->eventDispatcher);
    }
    
    public function testShouldRegisterUser(): void
    {
        // Arrange
        $name = 'John Doe';
        $email = 'john@example.com';
        $password = 'P@ssw0rd123';
        
        $request = new RegisterUserRequest($name, $email, $password);
        
        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);
            
        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class));
            
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch');
        
        // Act
        $response = $this->useCase->execute($request);
        
        // Assert
        $this->assertEquals($name, $response->toArray()['name']);
        $this->assertEquals($email, $response->toArray()['email']);
    }
    
    public function testShouldThrowExceptionWhenUserExists(): void
    {
        // Arrange
        $email = 'john@example.com';
        $request = new RegisterUserRequest('John Doe', $email, 'P@ssw0rd123');
        
        // Create a real User object instead of mocking it
        $existingUser = User::create(
            UserName::fromString('John Doe'),
            UserEmail::fromString($email),
            UserPassword::fromPlainPassword('P@ssw0rd123')
        );
        
        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($existingUser);
            
        // Assert
        $this->expectException(UserAlreadyExistsException::class);
        
        // Act
        $this->useCase->execute($request);
    }
} 