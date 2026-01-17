<?php

namespace App\Tests\Unit\Application\User\Command;

use App\Application\User\Command\RegisterUserCommand;
use App\Application\User\Command\RegisterUserHandler;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\User\ValueObject\HashedPassword;
use App\Domain\User\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class RegisterUserHandlerTest extends TestCase
{
    private UserRepositoryInterface $userRepository;
    private PasswordHasherInterface $passwordHasher;
    private RegisterUserHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->passwordHasher = $this->createMock(PasswordHasherInterface::class);

        $this->handler = new RegisterUserHandler(
            $this->userRepository,
            $this->passwordHasher
        );
    }

    public function testRegisterUserSuccessfully(): void
    {
        // Arrange (prepara)
        $command = new RegisterUserCommand(
            'test@example.com',
            'password123',
            'password123'
        );

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);

        $this->passwordHasher
            ->expects($this->once())
            ->method('hash')
            ->with('password123')
            ->willReturn(new HashedPassword('hashed_password'));

        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $result = $this->handler->handle($command);

        $this->assertInstanceOf(UserId::class, $result);
    }
}
