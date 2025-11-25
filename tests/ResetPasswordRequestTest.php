<?php

namespace App\Tests\Entity;

use App\Entity\ResetPasswordRequest;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ResetPasswordRequestTest extends TestCase
{
    private User $user;
    private \DateTimeInterface $expiresAt;
    private string $selector;
    private string $hashedToken;

    protected function setUp(): void
    {
        $this->user = $this->createMock(User::class);
        $this->expiresAt = new \DateTime('+1 hour');
        $this->selector = 'test_selector_123';
        $this->hashedToken = 'hashed_token_456';
    }

    public function testConstructor(): void
    {
        $request = new ResetPasswordRequest(
            $this->user,
            $this->expiresAt,
            $this->selector,
            $this->hashedToken
        );

        $this->assertInstanceOf(ResetPasswordRequest::class, $request);
    }

    public function testGetIdReturnsNullByDefault(): void
    {
        $request = new ResetPasswordRequest(
            $this->user,
            $this->expiresAt,
            $this->selector,
            $this->hashedToken
        );

        $this->assertNull($request->getId());
    }

    public function testGetUser(): void
    {
        $request = new ResetPasswordRequest(
            $this->user,
            $this->expiresAt,
            $this->selector,
            $this->hashedToken
        );

        $this->assertSame($this->user, $request->getUser());
    }

    public function testImplementsResetPasswordRequestInterface(): void
    {
        $request = new ResetPasswordRequest(
            $this->user,
            $this->expiresAt,
            $this->selector,
            $this->hashedToken
        );

        $this->assertInstanceOf(
            \SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface::class,
            $request
        );
    }

    public function testUsesResetPasswordRequestTrait(): void
    {
        $request = new ResetPasswordRequest(
            $this->user,
            $this->expiresAt,
            $this->selector,
            $this->hashedToken
        );

        // Vérifie que les méthodes du trait sont disponibles
        $this->assertTrue(method_exists($request, 'getExpiresAt'));
        $this->assertTrue(method_exists($request, 'getHashedToken'));
        $this->assertTrue(method_exists($request, 'getRequestedAt'));
    }

    public function testGetExpiresAt(): void
    {
        $request = new ResetPasswordRequest(
            $this->user,
            $this->expiresAt,
            $this->selector,
            $this->hashedToken
        );

        $this->assertSame($this->expiresAt, $request->getExpiresAt());
    }

    public function testGetHashedToken(): void
    {
        $request = new ResetPasswordRequest(
            $this->user,
            $this->expiresAt,
            $this->selector,
            $this->hashedToken
        );

        $this->assertSame($this->hashedToken, $request->getHashedToken());
    }

    public function testGetRequestedAt(): void
    {
        $before = new \DateTimeImmutable();
        
        $request = new ResetPasswordRequest(
            $this->user,
            $this->expiresAt,
            $this->selector,
            $this->hashedToken
        );

        $after = new \DateTimeImmutable();
        $requestedAt = $request->getRequestedAt();

        $this->assertInstanceOf(\DateTimeImmutable::class, $requestedAt);
        $this->assertGreaterThanOrEqual($before->getTimestamp(), $requestedAt->getTimestamp());
        $this->assertLessThanOrEqual($after->getTimestamp(), $requestedAt->getTimestamp());
    }

    public function testIsExpired(): void
    {
        // Test avec un token expiré
        $expiredDate = new \DateTime('-1 hour');
        $expiredRequest = new ResetPasswordRequest(
            $this->user,
            $expiredDate,
            $this->selector,
            $this->hashedToken
        );

        $this->assertTrue($expiredRequest->isExpired());

        // Test avec un token valide
        $validDate = new \DateTime('+1 hour');
        $validRequest = new ResetPasswordRequest(
            $this->user,
            $validDate,
            $this->selector,
            $this->hashedToken
        );

        $this->assertFalse($validRequest->isExpired());
    }

    public function testResetPasswordRequestComplet(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn('test@example.com');
        
        $expiresAt = new \DateTime('+2 hours');
        $selector = 'unique_selector_789';
        $hashedToken = 'unique_hashed_token_012';

        $request = new ResetPasswordRequest(
            $user,
            $expiresAt,
            $selector,
            $hashedToken
        );

        $this->assertSame($user, $request->getUser());
        $this->assertSame($expiresAt, $request->getExpiresAt());
        $this->assertSame($hashedToken, $request->getHashedToken());
        $this->assertFalse($request->isExpired());
        $this->assertInstanceOf(\DateTimeImmutable::class, $request->getRequestedAt());
    }

    public function testDifferentExpirationTimes(): void
    {
        $times = [
            new \DateTime('+30 minutes'),
            new \DateTime('+1 hour'),
            new \DateTime('+2 hours'),
            new \DateTime('+1 day'),
        ];

        foreach ($times as $time) {
            $request = new ResetPasswordRequest(
                $this->user,
                $time,
                $this->selector,
                $this->hashedToken
            );

            $this->assertSame($time, $request->getExpiresAt());
            $this->assertFalse($request->isExpired());
        }
    }
}