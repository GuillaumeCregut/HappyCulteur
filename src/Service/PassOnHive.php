<?php

namespace App\Service;

use App\Entity\Hive;
use App\Entity\Apiculteur;
use App\Constant\HiveState;
use App\Service\Archive\VisitArchiver;
use App\Service\Archive\HarvestArchiver;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Archive\DataloggerArchiver;

class PassOnHive
{
    public function __construct(
        private EntityManagerInterface $em,
        private DataloggerArchiver $dlArchiver,
        private VisitArchiver $visitArchiver,
        private HarvestArchiver $harvestArchiver,
    ) {}

    public function passOn(
        Hive $hive,
        Apiculteur $oldUser,
        Apiculteur $newUser,
        string $rootPath,
        ?bool $keepApiary = false
    ): void {
        $hive->setCoordX(null);
        $hive->setCoordY(null);
        $hive->setCoordZ(null);

        $hive->setQrCode(null);

        if (!$keepApiary) {
            $hive->setApiary(null);
            $hive->setState(HiveState::STOCK_HIVE);
        }

        $this->visitArchiver->archiveHive($hive, $oldUser);
        $this->harvestArchiver->archiveHive($hive, $oldUser);
        $this->dlArchiver->archiveHive($hive, $oldUser);

        if (null !== $hive->getDataloggerName()) {
            unlink($rootPath . $hive->getDataloggerName());
            $hive->setDataloggerName(null);
        }

        if (null !== $hive->getSwarm()) {
            $swarm = $hive->getSwarm();
            $swarm->setBeekeeper($newUser);
        }
        $hive->setObservation("transmise depuis {$oldUser->getFirstname()} {$oldUser->getName()}");
        $hive->setDate(null);
        $hive->setName("TR-{$hive->getName()}");
        $hive->setBeekeeper($newUser);
        $this->em->flush();
    }
}
