<?php

namespace App\Service;

use App\Constant\HiveState;
use App\Entity\Apiary;
use App\Entity\Apiculteur;
use App\Entity\Hive;
use App\Repository\HiveRepository;
use App\Tool\PathMaker;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Filesystem\Filesystem;

class UserConnected
{

    public function cleanUp(Apiculteur $user, string $uploadPath): void
    {
        $test = [];
        $relativePaths = PathMaker::getCleanPaths($user);
        foreach ($relativePaths as $key => $relative) {
            $fullPath = $uploadPath . $relative;
            $test[] = $fullPath;
            $this->removePath($fullPath);
        }
    }

    public function removeHivesFromSharedApiary(Apiculteur $user, Apiary $apiary, HiveRepository $repo, EntityManagerInterface $em): void
    {
        /**@var Hive[] $hives */
        $hives = $repo->findHiveByApiaryAndOwner($apiary, $user);
        foreach($hives as $hive) {
            $hive->setApiary(null);
            $hive->setState(HiveState::STOCK_HIVE);
        }
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
