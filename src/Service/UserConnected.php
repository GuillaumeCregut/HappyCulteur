<?php

namespace App\Service;

use App\Entity\Apiculteur;
use App\Tool\PathMaker;
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
