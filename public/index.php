<?php

use App\Domain\User\Repository\UserRepositoryInterface;
use App\Infrastructure\Doctrine\DoctrineUserRepository;
use App\Infrastructure\Event\SimpleEventDispatcher;
use App\Infrastructure\Event\EventDispatcherInterface;
use App\Infrastructure\Event\Listener\WelcomeEmailListener;
use App\Domain\User\Event\UserRegisteredEvent;
use App\Application\User\Register\RegisterUserUseCase;
use App\UI\Http\Controller\RegisterUserController;

require_once __DIR__ . '/../config/bootstrap.php';

// Print for debugging (now goes to log)
error_log("Starting application...");

// Headers to allow requests from any origin during development
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// For OPTIONS requests (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    error_log("Handling OPTIONS request");
    http_response_code(200);
    exit;
}

// We only accept POST requests for registration
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Method not allowed: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode([
        'status' => 'error',
        'code' => 405,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Get the request body
$requestBody = file_get_contents('php://input');
error_log("Request body: " . $requestBody);
$requestData = json_decode($requestBody, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("Invalid JSON: " . json_last_error_msg());
    echo json_encode([
        'status' => 'error',
        'code' => 400,
        'message' => 'Invalid JSON in request body'
    ]);
    exit;
}

error_log("Parsed request data: " . print_r($requestData, true));

// Dependencies configuration
error_log("Loading dependencies...");
$entityManager = require __DIR__ . '/../config/bootstrap.php';
$userRepository = new DoctrineUserRepository($entityManager);
$eventDispatcher = new SimpleEventDispatcher();

// Register the listener for the user registered event
$eventDispatcher->addListener(
    UserRegisteredEvent::class,
    new WelcomeEmailListener()
);

// Create the use case
$registerUserUseCase = new RegisterUserUseCase(
    $userRepository,
    $eventDispatcher
);

// Create the controller
$controller = new RegisterUserController($registerUserUseCase);

error_log("Executing controller...");
// Execute the controller
$response = $controller($requestData);

error_log("Controller response: " . print_r($response, true));

// Respond with the appropriate HTTP code
http_response_code($response['status'] === 'success' ? 201 : $response['code']);
echo json_encode($response); 