<?php

namespace App\Controller\Archive;

use App\Entity\Hive;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/archive', name: 'app_archive_')]
final class ArchiveController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('archive/index.html.twig', []);
    }

    #[Route('/hive/{id}', name: 'hive')]
    public function hive(
        Hive $hive,

    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        return $this->render('archive/hive/index.html.twig', [
            'hive' => $hive
        ]);
    }
}
