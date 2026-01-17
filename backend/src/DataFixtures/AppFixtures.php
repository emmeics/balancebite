<?php

namespace App\DataFixtures;

use App\Domain\User\Entity\User;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\User\ValueObject\Email;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(
        private PasswordHasherInterface $password_hasher,
    ) {
    }

    /*
    * Register a Test User
    */
    public function load(ObjectManager $manager): void
    {
        $email = new Email('test@example.com');
        $password = $this->password_hasher->hash('password123');

        $user = User::register($email, $password);

        $manager->persist($user);
        $manager->flush();
    }
}
