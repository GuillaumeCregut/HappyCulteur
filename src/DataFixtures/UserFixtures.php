<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Apiculteur;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher) {}
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
      /*    $admin = new Apiculteur();
        $admin->setFirstname('Guillaume')
            ->setName('Crégut')
            ->setLogin('gcregut')
            ->setCity('Saint Règle')
            ->setStreet('de Juscors')
            ->setStreetNumber('9')
            ->setZipCode('37530')
            ->setNumagri('123456')
            ->setRoles(['ROLE_ADMIN'])
            ->setCodeAPI('456789');
        $pass = $this->userPasswordHasher->hashPassword($admin, '12345678');
        $admin->setPassword($pass);
        $manager->persist($admin);*/

        for ($i = 0; $i < 5; $i++) {
            $user = new Apiculteur();
            $user->setFirstname($faker->firstName())
                ->setName($faker->lastName())
                ->setLogin($faker->userName())
                ->setCity($faker->city())
                ->setStreetNumber($faker->buildingNumber())
                ->setStreet($faker->streetName())
                ->setZipCode($faker->postcode())
                ->setNumagri('123456')
                ->setCodeAPI('456789');
                $pass = $this->userPasswordHasher->hashPassword($user, 'password');
                $user->setPassword($pass);
                $manager->persist($user);
        }
        $manager->flush();
    }
}
