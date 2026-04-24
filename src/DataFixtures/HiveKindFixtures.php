<?php

namespace App\DataFixtures;

use App\Entity\HiveKind;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HiveKindFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $kinds = [
            'Dadant' => 'admin\hives\dadant',
            'Ruchette' => 'admin\hives\ruchette',
            'Warré' => 'admin\hives\warre',
            'Langstroth' => 'admin\hives\langstroth',
            'Autre' => 'admin\hives\autre'
        ];
        $i = 0;
        foreach ($kinds as $name => $link) {
            $kind = new HiveKind();
            $kind->setName($name);
            $kind->setPicture($link);
            $manager->persist($kind);
            $this->addReference('kind_' . $i, $kind);
            $i++;
        }
        $manager->flush();
    }
}
