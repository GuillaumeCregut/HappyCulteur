<?php

namespace App\Service;

use Exception;
use App\Entity\Hive;
use App\Entity\Visit;
use App\Entity\Apiary;
use App\Tool\PathMaker;
use App\Entity\Apiculteur;
use App\Entity\Datalogger;
use App\Constant\HiveState;
use App\Entity\Archive\Harvest;
use App\Repository\HiveRepository;
use App\Repository\VisitRepository;
use App\Repository\ApiaryRepository;
use App\Repository\DataloggerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Archive\Visit as ArchiveVisit;
use App\Repository\Archive\HarvestRepository;
use App\Entity\Archive\Datalogger as ArchiveDl;
use App\Repository\Archive\DataloggerRepository as ArchiveDlRepo;

class UserConnected
{

    public function cleanUp(Apiculteur $user, string $uploadPath): void
    {
        $relativePaths = PathMaker::getCleanPaths($user);
        foreach ($relativePaths as $key => $relative) {
            $fullPath = $uploadPath . $relative;
            $this->removePath($fullPath);
        }
    }

    public function removeHivesFromSharedApiary(Apiculteur $user, Apiary $apiary, HiveRepository $repo, EntityManagerInterface $em): void
    {
        /**@var Hive[] $hives */
        $hives = $repo->findHiveByApiaryAndOwner($apiary, $user);
        foreach ($hives as $hive) {
            $hive->setApiary(null);
            $hive->setState(HiveState::STOCK_HIVE);
        }
        $em->flush();
    }

    public function RemoveUser(Apiculteur $user, string $uploadPath, EntityManagerInterface $em): void
    {
        $harvests = $user->getHarvests();
        foreach ($harvests as $harvest) {
            $em->remove($harvest);
        }

        /**@var HarvestRepository $repo */
        $repo = $em->getRepository(Harvest::class);
        $archiveHarvests = $repo->findByBeekeeper($user);
        foreach ($archiveHarvests as $harvest) {
            $em->remove($harvest);
        }

        /**@var VisitRepository $visitRepo */
        $visitRepo =  $em->getRepository(Visit::class);
        $visits = $visitRepo->findByBeekeper($user);
        foreach ($visits as $visit) {
            $em->remove($visit);
        }

        /**@var VisitRepository $visitRepo */
        $visitArchiveRepo =  $em->getRepository(ArchiveVisit::class);
        $visits = $visitArchiveRepo->findByBeekeeper($user);
        foreach ($visits as $visit) {
            $em->remove($visit);
        }

        $swarms = $user->getSwarms();
        foreach ($swarms as $swarm) {
            $em->remove($swarm);
        }

        /**@var DataloggerRepository $dlRepo */
        $dlRepo = $em->getRepository(Datalogger::class);
        $dls = $dlRepo->findByBeekeeper($user);
        foreach ($dls as $dl) {
            $em->remove($dl);
        }

        /**@var ArchiveDlRepo  $dlArchiveRepo */
        $dlArchiveRepo = $em->getRepository(ArchiveDL::class);
        $dls =  $dlArchiveRepo->findByBeekeeper($user);
        foreach ($dls as $dl) {
            $em->remove($dl);
        }

        $purchases = $user->getPurchases();
        foreach ($purchases as $purchase) {
            $em->remove($purchase);
        }

        $hives = $user->getHives();
        foreach ($hives as $hive) {
            $em->remove($hive);
        }

        /**@var ApiaryRepository  $apiaryRepo */
        $apiaryRepo = $em->getRepository(Apiary::class);
        $sharedApiaries = $apiaryRepo->findByBeekeeperInShared($user);
        /**@var Apiary[] $sharedApiaries */
        foreach ($sharedApiaries as $sharedApiarie) {
            $sharedApiarie->removeBeekeeper($user);
        }

        $ownerApiaries = $user->getApiaries();
        foreach ($ownerApiaries as $apiary) {
            $hives = $apiary->getHives();
            foreach ($hives as $hive) {
                $hive->setState(HiveState::STOCK_HIVE);
                $hive->setApiary(null);
            }
            $em->remove($apiary);
        }

        $this->cleanUp($user, $uploadPath);
        $userFolder = PathMaker::makeUserPath($user);
        $fullPath = $uploadPath . $userFolder;
        $this->removePath($fullPath);

        $em->flush();
    }

    private function removePath(string $path): void
    {
        $fileSystem = new Filesystem();
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $items = glob($path . '*');
        foreach ($items as $item) {
            try {
                if (is_dir($item)) {
                    $fileSystem->remove($item);
                } else {
                    unlink($item);
                }
            } catch (Exception $e) {
            }
        }
    }
}
