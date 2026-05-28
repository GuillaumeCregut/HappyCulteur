<?php

namespace App\Service\Archive;

use App\Entity\Apiculteur;
use App\Entity\Archive\Visit;
use App\Entity\Hive;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class VisitArchiver
{
    public function __construct(private EntityManagerInterface $em) {}

    public function archiveHive(Hive $hive, Apiculteur $user): array
    {
        $visits = $hive->getVisits();
        $count = 0;
        $success = true;
        try {
            foreach ($visits as $visit) {
                $array[] = $visit;
                $archive = new Visit();
                $archive->setDate($visit->getDate());
                $archive->setBeekeeper($user);
                $archive->setBehaviour($visit->getBehaviour());
                $hiveName = "{$hive->getName()} - {$hive->getIdentification()}";
                $archive->setHive($hiveName);
                $archive->setTemperature($visit->getTemperature());
                $archive->setHygrometry($visit->getHygrometry());
                $archive->setIsDisease($visit->isDisease());
                $archive->setIsFeeded($visit->isFeeded());
                $archive->setIsWorkToDo($visit->isWorksToDo());
                $archive->setIsQueenVisible($visit->isQueenVisible());
                $archive->setFeeding($visit->getFeeding());
                $archive->setNotes($visit->getNotes());
                $weather = $visit->getWeather()->label();
                $archive->setWeather($weather);
                $disease = $visit->getDisease()->getName();
                $archive->setDisease($disease);
                $this->em->persist($archive);
                $this->em->remove($visit);
                $count++;
            }
            $this->em->flush();
        } catch (Exception $e) {
            $success = false;
        }

        return ['count' => $count, 'success' => $success];
    }
}
