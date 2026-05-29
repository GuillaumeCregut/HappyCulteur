<?php

namespace App\Service\Archive;

use App\Entity\Apiculteur;
use App\Entity\Archive\Harvest;
use App\Entity\Hive;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class HarvestArchiver
{
    public function __construct(private EntityManagerInterface $em) {}

    public function archiveHive(Hive $hive, Apiculteur $user): array
    {
        $harvests = $hive->getHarvests();
        $count = 0;
        $success = true;
        try {
            foreach ($harvests as $harvest) {
                $archive = new Harvest();
                $archive->setDate($harvest->getDate());
                $archive->setBeekeeper($user);
                $hiveName = "{$hive->getName()} - {$hive->getIdentification()}";
                $archive->setHive($hiveName);
                $archive->setWeight($harvest->getWeight());
                $archive->setHoneyKind($harvest->getHoneyKind()->getName());
                $archive->setPicture($harvest->getHoneyKind()->getPicture());
                $this->em->persist($archive);
                $this->em->remove($harvest);
                $count++;
            }
            $this->em->flush();
        } catch (Exception $e) {
            $success = false;
        }

        return ['count' => $count, 'success' => $success];
    }
}
