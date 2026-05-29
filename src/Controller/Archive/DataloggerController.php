<?php

namespace App\Controller\Archive;

use App\Entity\Hive;
use App\Entity\Apiculteur;
use App\Repository\Archive\DataloggerRepository;
use App\Service\Archive\DataloggerArchiver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/archive/datalogger', name: 'app_archive_datalogger_')]
final class DataloggerController extends AbstractController
{
    #[Route('/archive/datalogger', name: 'index')]
    public function index(): Response
    {
        return $this->render('archive/datalogger/index.html.twig', []);
    }

    #[Route('/hive/{id}', name: 'hive', methods: ['GET', 'POST'])]
    public function hive(
        Hive $hive,
        Request $request,
        #[CurrentUser] Apiculteur $user,
        DataloggerArchiver $archiver,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        if ($request->isMethod('POST')) {
            if (
                !$this->isCsrfTokenValid(
                    'archive_item',
                    $request->request->get('_token')
                )
            ) {
                throw $this->createAccessDeniedException();
            }
            $result = $archiver->archiveHive($hive, $user);
            $count = $result['count'];
            $success = $result['success'];
            $message = 'success';
            if (!$success) {
                $message = 'warning';
            }
            $this->addFlash($message, "{$count} données archivées avec succès.");
            return $this->redirectToRoute('app_archive_hive', ['id' => $hive->getId()]);
        }

        return $this->render('archive/datalogger/hive.html.twig', [
            'hive' => $hive
        ]);
    }

    #[Route('/hive/{id}/consult', name: 'hive_consult', methods: ['GET'])]
    public function consultHive(
        Hive $hive,
        DataloggerRepository $repo,
        #[CurrentUser] Apiculteur $user,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $archives = $repo->findByHiveAndBeekeeper($hive, $user);
        return $this->render('archive/datalogger/hive_consult.html.twig', [
            'hive' => $hive,
            'archives' => $archives
        ]);
    }
}
