<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Apiary;
use App\Entity\Apiculteur;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ApiaryFixtures extends Fixture  implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        //admin apiaries
        for ($i = 0; $i < 5; $i++) {
            $apiary = new Apiary();
            $apiary->setName('Rucher_' . $i)
                ->setIsActive($i % 2 === 0)
                ->setLocalisation($faker->city())
                ->setIdentification($faker->bothify('???###'));
            $user = $this->getReference('admin', Apiculteur::class);
            $apiary->setBeekeeper($user);
            $manager->persist($apiary);
            $this->addReference('apiary_admin_' . $i, $apiary);
        }
        for ($i = 0; $i < 20; $i++) {
            $apiary = new Apiary();
            $apiary->setName('Rucher_' . $i)
                ->setIsActive($i % 2 === 0)
                ->setLocalisation($faker->city())
                ->setIdentification($faker->bothify('???###'));
            $user = $this->getReference('user_' .  $faker->numberBetween(0, 4), Apiculteur::class);
            $apiary->setBeekeeper($user);
            $manager->persist($apiary);
            $this->addReference('apiary_' . $i, $apiary);
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
