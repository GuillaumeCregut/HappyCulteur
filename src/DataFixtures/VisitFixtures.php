<?php

namespace App\DataFixtures;

use App\Constant\Weather;
use App\Entity\Disease;
use Faker\Factory;
use App\Entity\Hive;
use App\Entity\Visit;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class VisitFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        //admin visits
        for($i = 0; $i <15; $i++) {
            $visit = new Visit();
            $date =  $faker->dateTimeBetween('-3 years', 'now');
            $dateVisit = DateTimeImmutable::createFromMutable($date);
            $hive = $this->getReference('hive_admin_' .  $faker->numberBetween(0, 4), Hive::class); 
            $visit->setIsDisease(false);
            if($faker->numberBetween(0, 100) % 4 === 0) {
                $visit->setIsDisease(true);
                $disease =  $this->getReference('disease_' .  $faker->numberBetween(0, 4), Disease::class);
                $visit->setDisease($disease);
            }
            $visit->setIsFeeded(false);
            if($faker->numberBetween(0, 100) % 3 === 0) {
                $visit->setIsFeeded(true);
                $visit->setFeeding($faker->sentence(3));
            }
            $visit->setIsQueenVisible($faker->numberBetween(0, 100) % 3 === 0)
                ->setIsWorksToDo($faker->numberBetween(0, 100) % 4 === 0)
                ->setHygrometry($faker->numberBetween(0, 100))
                ->setTemperature($faker->randomFloat(1, -10, 40))
                ->setWeight($faker->randomFloat(1, 15, 40))
                ->setNotes($faker->paragraph())
                ->setBehaviour($faker->sentence(3))
                ->setPopulation($faker->sentence(3))
                ->setDate($dateVisit)
                ->setHive($hive)
                ->setWeather($this->randomState());
            $manager->persist($visit);
        }
        
        for($i = 0; $i <100; $i++) {
            $visit = new Visit();
            $date =  $faker->dateTimeBetween('-3 years', 'now');
            $dateVisit = DateTimeImmutable::createFromMutable($date);
            $hive = $this->getReference('hive_' .  $faker->numberBetween(0, 49), Hive::class); 
            $visit->setIsDisease(false);
            if($faker->numberBetween(0, 100) % 4 === 0) {
                $visit->setIsDisease(true);
                $disease =  $this->getReference('disease_' .  $faker->numberBetween(0, 4), Disease::class);
                $visit->setDisease($disease);
            }
            $visit->setIsFeeded(false);
            if($faker->numberBetween(0, 100) % 3 === 0) {
                $visit->setIsFeeded(true);
                $visit->setFeeding($faker->sentence(3));
            }
            $visit->setIsQueenVisible($faker->numberBetween(0, 100) % 3 === 0)
                ->setIsWorksToDo($faker->numberBetween(0, 100) % 4 === 0)
                ->setHygrometry($faker->numberBetween(0, 100))
                ->setTemperature($faker->randomFloat(1, -10, 40))
                ->setWeight($faker->randomFloat(1, 15, 40))
                ->setNotes($faker->paragraph())
                ->setBehaviour($faker->sentence(3))
                ->setPopulation($faker->sentence(3))
                ->setDate($dateVisit)
                ->setHive($hive)
                ->setWeather($this->randomState());
            $manager->persist($visit);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DiseaseFixtures::class,
            HiveFixtures::class,
        ];
    }

     private function randomState(): Weather
    {
        $cases      = Weather::cases();
        $randomCase = $cases[array_rand($cases)];
        return $randomCase;
    }
}
