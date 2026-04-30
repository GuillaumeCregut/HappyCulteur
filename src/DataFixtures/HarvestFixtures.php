<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Hive;
use App\Entity\Honey;
use DateTimeImmutable;
use App\Entity\Harvest;
use App\Entity\Apiculteur;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class HarvestFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        for($i = 0; $i < 50; $i++) {
            $harvest = new Harvest();
            $user = $this->getReference('admin', Apiculteur::class); 
            $hive = $this->getReference('hive_admin_' .  $faker->numberBetween(0, 4), Hive::class);
            $honey = $this->getReference('honey_' . $faker->numberBetween(0, 6), Honey::class);
            $date =  $faker->dateTimeBetween('-3 years', 'now');
            $dateHarvest = DateTimeImmutable::createFromMutable($date); 
            $harvest->setBeekeeper($user)
                ->setDate($dateHarvest)
                ->setHive($hive)
                ->setHoneyKind($honey)
                ->setWeight($faker->randomFloat(2, 3,15));
                $manager->persist($harvest);
        }

         for($i = 0; $i < 50; $i++) {
            $harvest = new Harvest();
            $user = $this->getReference('user_' .  $faker->numberBetween(0, 4), Apiculteur::class);
            $honey = $this->getReference('honey_' . $faker->numberBetween(0, 6), Honey::class);
            $date =  $faker->dateTimeBetween('-3 years', 'now');
            $dateHarvest = DateTimeImmutable::createFromMutable($date); 
            $harvest->setBeekeeper($user)
                ->setDate($dateHarvest)
                ->setHoneyKind($honey)
                ->setWeight($faker->randomFloat(2, 3,15));
                $manager->persist($harvest);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            HiveFixtures::class,
            HoneyFixtures::class,
        ];
    }
}
