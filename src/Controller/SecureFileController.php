<?php

namespace App\Controller;

use App\Entity\Hive;
use App\Entity\Apiary;
use App\Tool\PathMaker;
use App\Entity\Apiculteur;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/secure', name: 'app_secure_')]
final class SecureFileController extends AbstractController
{
    public function __construct(private readonly string $userFolderRoot) {}

    #[Route('/declaration/{id}', name: 'declaration')]
    public function index(
        #[CurrentUser] Apiculteur $user,
        Apiary $apiary
    ): Response {
        if ($user !== $apiary->getBeekeeper()) {
            throw $this->createAccessDeniedException();
        }
        $relativePath = PathMaker::makeApiaryDeclarationPath($user, $this->userFolderRoot);
        $fullPath = $this->userFolderRoot . $relativePath;
        $filename = "declaration_{$apiary->getIdentification()}.pdf";
        $fullPath .= $filename;
        return $this->file($fullPath);
    }

     #[Route('/datalogger/{id}', name: 'datalogger')]
    public function datalogger(
        Hive $hive
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $filename = $this->userFolderRoot . $hive->getDataloggerName();
        return $this->file($filename);
    }
}
