<?php

namespace App\Service\Archive;

use Exception;
use App\Entity\Hive;
use App\Entity\Apiculteur;
use App\Entity\Archive\Datalogger;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Datalogger as DataloggerEntity;

class DataloggerArchiver
{
    public function __construct(private EntityManagerInterface $em) {}

    public function archiveHive(Hive $hive, Apiculteur $user): array
    {
        $count = 0;
        $success = true;
        /**@var DataloggerEntity[] $logs */
        $logs = $hive->getDatalogger()->toArray();
        try {
            foreach ($logs as $log) {
                $archive = new Datalogger();
                $archive->setDateTime($log->getDateTime());
                $archive->setBeekeeper($user);
                $hiveName = "{$hive->getName()} - {$hive->getIdentification()}";
                $archive->setHive($hiveName);
                $archive->setWeight($log->getWeight());
                $archive->setExtHygro($log->getExtHyrgo());
                $archive->setIntHygro($log->getIntHygro());
                $archive->setIntTemp($log->getIntTemp());
                $archive->setExtTemp($log->getExtTemp());
                $archive->setIdentification($log->getIdentification());
                $this->em->persist($archive);
                $this->em->remove($log);
                $count++;
            }
            $this->em->flush();
        } catch (Exception $e) {
            $success = false;
        }

        return ['count' => $count, 'success' => $success];
    }
}
