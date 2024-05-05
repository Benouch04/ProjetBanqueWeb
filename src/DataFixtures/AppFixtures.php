<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Users;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = new Users();
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'password123' 
        ));
        $user->setUsername('admin');
        $user->setLastname('admin');
        $user->setFirstname('admin');
        $user->setType('Directeur');
        $user->setRoles(['ROLE_DIRECTEUR']);

        $manager->persist($user);
        $manager->flush();
    }
}