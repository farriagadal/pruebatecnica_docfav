<?php

namespace App\UI\Http\Controller;

use App\Application\User\Register\RegisterUserRequest;
use App\Application\User\Register\RegisterUserUseCase;
use App\Domain\User\Exception\InvalidEmailException;
use App\Domain\User\Exception\WeakPasswordException;
use App\Domain\User\Exception\UserAlreadyExistsException;

final class RegisterUserController
{
    private RegisterUserUseCase $registerUserUseCase;

    public function __construct(RegisterUserUseCase $registerUserUseCase)
    {
        $this->registerUserUseCase = $registerUserUseCase;
    }

    public function __invoke(array $requestData): array
    {
        error_log("RegisterUserController: Starting controller execution");
        error_log("RegisterUserController: Request data: " . print_r($requestData, true));
        
        try {
            $request = new RegisterUserRequest(
                $requestData['name'] ?? '',
                $requestData['email'] ?? '',
                $requestData['password'] ?? ''
            );
            
            error_log("RegisterUserController: Created request object");

            $response = $this->registerUserUseCase->execute($request);
            
            error_log("RegisterUserController: Use case executed successfully");

            return [
                'status' => 'success',
                'data' => $response->toArray()
            ];
        } catch (InvalidEmailException $e) {
            error_log("RegisterUserController: Invalid email exception: " . $e->getMessage());
            return $this->errorResponse('Invalid email format', 400);
        } catch (WeakPasswordException $e) {
            error_log("RegisterUserController: Weak password exception: " . $e->getMessage());
            return $this->errorResponse($e->getMessage(), 400);
        } catch (UserAlreadyExistsException $e) {
            error_log("RegisterUserController: User already exists exception: " . $e->getMessage());
            return $this->errorResponse('Email already in use', 409);
        } catch (\Throwable $e) {
            error_log("RegisterUserController: Unexpected error: " . $e->getMessage());
            error_log("RegisterUserController: Error trace: " . $e->getTraceAsString());
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    private function errorResponse(string $message, int $code): array
    {
        error_log("RegisterUserController: Returning error response: $message (code: $code)");
        return [
            'status' => 'error',
            'code' => $code,
            'message' => $message
        ];
    }
} 