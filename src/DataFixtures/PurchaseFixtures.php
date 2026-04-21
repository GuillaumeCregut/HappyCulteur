<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Purchase;
use App\Entity\Apiculteur;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PurchaseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $admin = $this->getReference('admin', Apiculteur::class);
        //admin purchases
        for ($i = 0; $i < 10; $i++) {
            $purchase = new Purchase();
            $date = $faker->date();
            $purchaseDate = new DateTimeImmutable($date);
            $purchase->setBeekeeper($admin)
                ->setAmount($faker->randomFloat(2,5,1000))
                ->setDate($purchaseDate)
                ->setDescription($faker->paragraph())
                ->setProvider($faker->company());
            $manager->persist($purchase);
        }

        for ($i = 0; $i < 50; $i++) {
            $purchase = new Purchase();
            $date = $faker->date();
            $purchaseDate = new DateTimeImmutable($date);
            $user = $this->getReference('user_' .  $faker->numberBetween(0, 4), Apiculteur::class);
            $purchase->setBeekeeper($user)
                ->setAmount($faker->randomFloat(2,5,1000))
                ->setDate($purchaseDate)
                ->setDescription($faker->paragraph())
                ->setProvider($faker->company());
            $manager->persist($purchase);
        } 
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
