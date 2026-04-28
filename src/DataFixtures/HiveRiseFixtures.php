<?php

namespace App\DataFixtures;

use App\Entity\HiveRise;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HiveRiseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         for($i=1; $i<13; $i++) {
            $rise = new HiveRise();
            $name = "Hausse {$i} cadres";
            $rise->setName($name);
            $manager->persist($rise);
            $this->addReference('rise_' . $i, $rise);
        }
        $manager->flush();
    }
}
