<?php

namespace App\DataFixtures;

use App\Entity\Honey;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HoneyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $honeys =[
            'Accacia', 
            'Toutes fleurs',
            'Chataignier',
            'Montagne',
            'trèfle',
            'pissenlit',
            'sarrasin'
        ];
        $i = 0;
        foreach($honeys as $name) {
            $honey = new Honey();
            $honey->setName($name);
            $honey->setPicture('admin/honeys/default.png');
            $manager->persist($honey);
            $this->addReference('honey_' . $i, $honey);
            $i++;
        }
        $manager->flush();
    }
}
