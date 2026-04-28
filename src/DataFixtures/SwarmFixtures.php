<?php

namespace App\DataFixtures;

use App\Constant\SwarmOrigin;
use Faker\Factory;
use App\Entity\Hive;
use App\Entity\Swarm;
use DateTimeImmutable;
use App\Entity\Apiculteur;
use App\DataFixtures\HiveFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SwarmFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for($i = 0; $i <10; $i++) {
            $swarm = new Swarm();
            $date = $faker->date();
            $swarmDate = new DateTimeImmutable($date);
            $swarm->setName("EssaimAdmin-{$i}")
                ->setDate($swarmDate)
                ->setCapturePlace($faker->city())
                ->setQueenAge($faker->randomDigit())
                ->setQueenOrigin($faker->sentence(3))
                ->setOrigin($this->randomOrigin())
                ->setSpecy($faker->word());
            $user = $this->getReference('admin', Apiculteur::class);
            $swarm->setBeekeeper($user);
            if($i < 5){
                $hive = $this->getReference('hive_admin_' .  $i, Hive::class);
                $swarm->setHive($hive);
            }
            $manager->persist($swarm);
        }

        for($i = 0; $i <10; $i++) {
            $swarm = new Swarm();
            $date = $faker->date();
            $swarmDate = new DateTimeImmutable($date);
            $swarm->setName("EssaimAdmin-{$i}")
                ->setDate($swarmDate)
                ->setCapturePlace($faker->city())
                ->setQueenAge($faker->randomDigit())
                ->setQueenOrigin($faker->sentence(3))
                ->setOrigin($this->randomOrigin())
                ->setSpecy($faker->word());
            $user = $this->getReference('user_' .  $faker->numberBetween(0, 4), Apiculteur::class);
            $swarm->setBeekeeper($user);
            $manager->persist($swarm);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            HiveFixtures::class,
        ];
    }

     private function randomOrigin(): SwarmOrigin
    {
        $cases = SwarmOrigin::cases();
        $randomCase = $cases[array_rand($cases)];
        return $randomCase;
    }
}
