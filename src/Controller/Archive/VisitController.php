<?php

namespace App\Controller\Archive;

use App\Entity\Hive;
use App\Entity\Apiculteur;
use App\Repository\Archive\VisitsRepository;
use App\Service\Archive\VisitArchiver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/archive/visit', name: 'app_archive_visit_')]
final class VisitController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(VisitsRepository $repo, #[CurrentUser] Apiculteur $user,): Response
    {
        $archives = $repo->findByBeekeeper($user);
        return $this->render('archive/visits/index.html.twig', [
            'archives' => $archives
        ]);
    }

    #[Route('/hive/{id}', name: 'hive', methods: ['GET', 'POST'])]
    public function archiveHive(
        Request $request,
        Hive $hive,
        VisitArchiver $archiver,
        #[CurrentUser] Apiculteur $user,
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
            $this->addFlash($message, "{$count} visites archivées avec succès.");
            return $this->redirectToRoute('app_archive_hive', ['id' => $hive->getId()]);
        }

        return $this->render('archive/visits/hive.html.twig', [
            'hive' => $hive
        ]);
    }

    #[Route('/hive/{id}/consult', name: 'hive_consult', methods: ['GET'])]
    public function consultHive(
        Hive $hive,
        VisitsRepository $repo,
        #[CurrentUser] Apiculteur $user,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $archives = $repo->findByHiveAndBeekeeper($hive, $user);
        return $this->render('archive/visits/hive_consult.html.twig', [
            'hive' => $hive,
            'archives' => $archives
        ]);
    }
}
