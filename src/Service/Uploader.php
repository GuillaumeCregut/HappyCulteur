<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class Uploader
{
    public function __construct(
        private SluggerInterface $slugger,
        private string $rootPath,
    ) {}

    public function storeFile(
        UploadedFile $file,
        string $role,
        string $documentType,
        ?string $filename,
        ?string $subFolder = null
    ): string {
        $baseFolder = $role . DIRECTORY_SEPARATOR . $documentType;
        $baseFolder .= $subFolder ? DIRECTORY_SEPARATOR . $subFolder : '';
        if (null === $filename) {
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        }
        $extension = $file->guessExtension();
        $slugName = $this->slugify($filename);
        $fullFolder = $this->rootPath . DIRECTORY_SEPARATOR . $baseFolder;
        try {
            $file->move($this->rootPath . DIRECTORY_SEPARATOR . $baseFolder, $slugName . '.' . $extension);
        } catch(FileException $e) {

        }
        $baseFolder .= DIRECTORY_SEPARATOR . $slugName . '.' . $extension;
        return $baseFolder;
    }

    private function slugify(string $name): string
    {
        $temp = $this->slugger->slug($name)->lower()->toString();
        return $temp;
    }
}
