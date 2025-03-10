<?php

namespace App\Application\User\Register;

use App\Domain\User\User;
use App\Domain\User\ValueObject\UserName;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserPassword;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Exception\UserAlreadyExistsException;
use App\Domain\User\Event\UserRegisteredEvent;
use App\Infrastructure\Event\EventDispatcherInterface;
use App\Application\User\UserResponse;

final class RegisterUserUseCase
{
    private UserRepositoryInterface $userRepository;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        UserRepositoryInterface $userRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute(RegisterUserRequest $request): UserResponse
    {
        error_log("RegisterUserUseCase: Starting execution");
        error_log("RegisterUserUseCase: Creating email value object");
        $email = UserEmail::fromString($request->email());
        
        error_log("RegisterUserUseCase: Checking if user already exists");
        $existingUser = $this->userRepository->findByEmail($email);
        
        if ($existingUser !== null) {
            error_log("RegisterUserUseCase: User already exists");
            throw new UserAlreadyExistsException($request->email());
        }
        
        error_log("RegisterUserUseCase: Creating user");
        $user = User::create(
            UserName::fromString($request->name()),
            $email,
            UserPassword::fromPlainPassword($request->password())
        );
        
        error_log("RegisterUserUseCase: Saving user");
        $this->userRepository->save($user);
        
        error_log("RegisterUserUseCase: Dispatching event");
        $this->eventDispatcher->dispatch(new UserRegisteredEvent($user));
        
        error_log("RegisterUserUseCase: Creating response");
        return UserResponse::fromUser($user);
    }
} 