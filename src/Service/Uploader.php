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

    /**
     * Will move the uploaded file to the defined directory
     *
     * @param UploadedFile $file
     * @param string $path absolute path of the folder to store file
     * @param string|null $filename the whished filename
     * @return string the file name with extension
     */
    public function storeFile(
        UploadedFile $file,
        string $path,
        ?string $filename
    ): string {
        if (null === $filename) {
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        }
        $extension = $file->guessExtension();
        $destinationName = $this->slugify($filename)  . '.' . $extension;
        
        try {
            $file->move($path, $destinationName);
        } catch (FileException $e) {
            throw new FileException($e->getMessage());
        }

        return $destinationName;
    }

    private function slugify(string $name): string
    {
        $temp = $this->slugger->slug($name)->lower()->toString();
        return $temp;
    }
}
