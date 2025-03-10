<?php

use App\Domain\User\Repository\UserRepositoryInterface;
use App\Application\User\Register\RegisterUserUseCase;
use App\UI\Http\Controller\RegisterUserController;

require_once __DIR__ . '/../config/bootstrap.php';

// Print for debugging (now goes to log)
error_log("Starting application...");


// Create the controller
$controller = new RegisterUserController($registerUserUseCase);

error_log("Executing controller...");
// Execute the controller
$response = $controller($requestData);

error_log("Controller response: " . print_r($response, true));

// Respond with the appropriate HTTP code
http_response_code($response['status'] === 'success' ? 201 : $response['code']);
echo json_encode($response); 