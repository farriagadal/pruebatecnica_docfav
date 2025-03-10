# User Registration System

A PHP application for user registration following Domain-Driven Design (DDD) and Hexagonal Architecture principles.

## Project Structure

```
project/
├── src/
│   ├── Application/     # Use cases
│   ├── Domain/          # Entities, Value Objects, Repository interfaces
│   ├── Infrastructure/  # Concrete implementations (Doctrine, etc.)
│   └── UI/              # Controllers, Response DTOs
├── tests/
│   ├── Unit/
│   └── Integration/
├── docker/
│   ├── php/
│   └── mysql/
├── public/              # Entry point
├── config/              # Configuration files
├── docker-compose.yml
├── Makefile
├── composer.json
└── README.md
```
## Requirements

- Docker & Docker Compose


## Main Execution Commands

Here are the main commands to run the application:

1. Start the Docker containers in detached mode:
```bash
docker-compose up -d --build
```

2. Install dependences:
```bash
docker-compose exec php composer install
```

3. Create the database schema:
```bash
docker-compose exec php php bin/doctrine orm:schema-tool:create
```

4. Run the tests:
```bash
docker-compose exec php ./vendor/bin/phpunit
```

## Usage

The application exposes a REST API endpoint for user registration:

```
POST http://localhost:8080/
```

Request body:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "StrongP@ssw0rd"
}
```

Response:
```json
{
  "status": "success",
  "data": {
    "id": "uuid-string",
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2023-01-01 12:00:00"
  }
}
```

## Contact

If you have any questions or need assistance, please feel free to contact me at:
farriagadal94@gmail.com
