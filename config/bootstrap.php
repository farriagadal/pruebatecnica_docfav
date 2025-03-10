<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Dotenv\Dotenv;
use App\Infrastructure\Doctrine\Type\UserIdType;
use App\Infrastructure\Doctrine\Type\UserNameType;
use App\Infrastructure\Doctrine\Type\UserEmailType;
use App\Infrastructure\Doctrine\Type\UserPasswordType;

require_once __DIR__ . '/../vendor/autoload.php';

error_log("Bootstrap: start...");
// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

error_log("Bootstrap: Configuring Doctrine...");
// Configure Doctrine
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [__DIR__ . '/../src'],
    isDevMode: true,
);

// Configure Doctrine to use the __toString() method to convert Value Objects to strings
$config->setQuoteStrategy(new \Doctrine\ORM\Mapping\DefaultQuoteStrategy());

// Register Doctrine custom types
error_log("Bootstrap: Registering custom types...");
try {
    Type::addType(UserIdType::NAME, UserIdType::class);
    Type::addType(UserNameType::NAME, UserNameType::class);
    Type::addType(UserEmailType::NAME, UserEmailType::class);
    Type::addType(UserPasswordType::NAME, UserPasswordType::class);
    error_log("Bootstrap: Custom types registered successfully");
} catch (\Exception $e) {
    error_log("Bootstrap: Error registering custom types: " . $e->getMessage());
}

error_log("Bootstrap: Setting up database connection...");
$connection = DriverManager::getConnection([
    'driver' => 'pdo_mysql',
    'host' => $_ENV['DB_HOST'],
    'port' => $_ENV['DB_PORT'],
    'dbname' => $_ENV['DB_NAME'],
    'user' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8mb4'
], $config);

error_log("Bootstrap: Creating EntityManager...");
$entityManager = new EntityManager($connection, $config);


error_log("Bootstrap: EntityManager created successfully");
return $entityManager; 