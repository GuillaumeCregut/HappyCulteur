<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Hive;
use DateTimeImmutable;
use App\Constant\HiveState;
use App\Entity\Apiary;
use App\Entity\HiveKind;
use App\Entity\HiveRise;
use App\Service\HiveProcessor;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class HiveFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 5; $i++) {
            $date = $faker->date();
            $hiveDate = new DateTimeImmutable($date);
            $hive = new Hive();
            $hive->setName("Ruche {$i}")
                ->setIdentification($faker->bothify('???###'))
                ->setDate($hiveDate)
                ->setState($this->randomState())
                ->setRiseNumber($faker->numberBetween(0, 9))
                ->setFrameNumber($faker->numberBetween(1, 12))
                ->setObservation($faker->paragraph());
            $apiary = $this->getReference('apiary_admin_' .  $faker->numberBetween(0, 4), Apiary::class);
            $hive->setApiary($apiary);
            $user = $apiary->getBeekeeper();
            $hive->setBeekeeper($user);
            $hive->setQrCode(HiveProcessor::generateQR($user, $hive));
            $kind = $this->getReference('kind_' .  $faker->numberBetween(0, 4), HiveKind::class);
            $hive->setKind($kind);
            $rise = $this->getReference('rise_' .  $faker->numberBetween(1, 12), HiveRise::class);
            $hive->setRise($rise);
            $this->addReference('hive_admin_' . $i, $hive);
            $manager->persist($hive);
        }

        for ($i = 0; $i < 50; $i++) {
            $date = $faker->date();
            $hiveDate = new DateTimeImmutable($date);
            $hive = new Hive();
            $hive->setName("Ruche {$i}")
                ->setIdentification($faker->bothify('???###'))
                ->setDate($hiveDate)
                ->setState($this->randomState())
                ->setRiseNumber($faker->numberBetween(0, 9))
                ->setFrameNumber($faker->numberBetween(1, 12))
                ->setObservation($faker->paragraph());
            $apiary = $this->getReference('apiary_' .  $faker->numberBetween(0, 19), Apiary::class);
            $hive->setApiary($apiary);
            $user = $apiary->getBeekeeper();
            $hive->setBeekeeper($user);
            $hive->setQrCode(HiveProcessor::generateQR($user, $hive));
            $kind = $this->getReference('kind_' .  $faker->numberBetween(0, 4), HiveKind::class);
            $hive->setKind($kind);
            $rise = $this->getReference('rise_' .  $faker->numberBetween(1, 12), HiveRise::class);
            $hive->setRise($rise);
            $this->addReference('hive_' . $i, $hive);
            $manager->persist($hive);
        }

        $manager->flush();
    }


    public function getDependencies(): array
    {
        return [
            ApiaryFixtures::class,
            HiveKindFixtures::class,
            HiveRiseFixtures::class,
            UserFixtures::class,
        ];
    }

    private function randomState(): HiveState
    {
        $cases      = HiveState::cases();
        $randomCase = $cases[array_rand($cases)];
        return $randomCase;
    }
}
