<?php

namespace App\Service;

use App\Entity\Apiculteur;
use App\Entity\Hive;
use Doctrine\ORM\EntityManagerInterface;

class PassOnHive
{
    public function __construct(EntityManagerInterface $em)
    {
        
    }

    public function passOn(Hive $hive, Apiculteur $newUser)
    {
        /*
            Supprimer les coordonnées GPS
            Changer le owner
            Supprimer le QRCode
            Mettre la ruche en état stock
            Si elle a un essaim, transmettre l'essaim aussi
            Archiver les visites
            Archiver les récoltes
            Archiver les dataloggers
            Supprimer le dataloggerName et les données liées
        */
    }
}