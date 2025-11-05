<?php

namespace App\Tests\Integration\Infrastructure\Security\Service;

use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserPassword;
use App\Infrastructure\Doctrine\Entity\User;
use App\Infrastructure\Security\Service\PasswordHasher;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PasswordHasherTest extends KernelTestCase
{
    private PasswordHasher $hasher;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->hasher = $container->get(PasswordHasher::class);
    }

    public function testHashAndVerifySuccess(): void
    {
        $user = new User();
        $user->setEmail(new UserEmail('user@example.com'));

        $password = new UserPassword('Password123');

        $hashPassword = $this->hasher->hash($user, $password);
        $user->setPassword($hashPassword);

        $this->assertNotEmpty((string)$hashPassword);

        $this->assertTrue($this->hasher->verify($user, $password));
        $this->assertFalse($this->hasher->verify($user, new UserPassword('OtherPassword1')));
    }
}
