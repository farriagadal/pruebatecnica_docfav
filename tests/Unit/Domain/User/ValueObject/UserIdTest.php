<?php

namespace Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    public function testShouldCreateIdFromValidUuid(): void
    {
        $uuid = '0e2f27de-1a1a-4448-9f63-a27c956bc8a6';
        $userId = UserId::fromString($uuid);
        
        $this->assertEquals($uuid, $userId->value());
    }
    
    public function testShouldThrowExceptionWhenIdIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        UserId::fromString('invalid-uuid');
    }
    
    public function testShouldGenerateValidUuid(): void
    {
        $userId = UserId::generate();
        
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            $userId->value()
        );
    }
    
    public function testShouldCompareIds(): void
    {
        $uuid = '0e2f27de-1a1a-4448-9f63-a27c956bc8a6';
        $userId1 = UserId::fromString($uuid);
        $userId2 = UserId::fromString($uuid);
        $userId3 = UserId::generate();
        
        $this->assertTrue($userId1->equals($userId2));
        $this->assertFalse($userId1->equals($userId3));
    }
} 