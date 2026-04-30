<?php

namespace App\DataFixtures;

use App\Entity\Disease;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class DiseaseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $names = [
            'Varoa',
            'Loque',
            'Nosémose',
            'Acariose',
            'Maladie Noire',
        ];
        $i = 0;
        foreach($names as $name){
            $disease = new Disease();
            $disease->setName($name);
            $manager->persist($disease);
            $this->addReference('disease_' . $i, $disease);
            $i++;
        }
        $manager->flush();
    }
}
