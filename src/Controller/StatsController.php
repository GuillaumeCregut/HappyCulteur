<?php

namespace App\Controller;

use App\Entity\Hive;
use App\Entity\Apiculteur;
use App\Service\Stats\Temps;
use App\Form\Stats\DatesType;
use App\Service\Stats\Visits;
use App\Service\Stats\Weight;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/stats', name: 'app_stats_')]
final class StatsController extends AbstractController
{

    public function __construct(private readonly string $userFolderRoot) {}

    #[Route('/hive/{id}', name: 'index')]
    public function index(
        Hive $hive
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        return $this->render('stats/index.html.twig', [
            'hive' => $hive,
        ]);
    }

    #[Route('/hive/{id}/visits', name: 'visits_search', methods: ['GET', 'POST'])]
    public function visits(
        Hive $hive,
        Request $request,
        Visits $visits,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $form = $this->createForm(DatesType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dates = $form->getData();
            $visits = $visits->getHiveVisits($hive, $dates, $this->userFolderRoot);
            $request->getSession()->set('visit_results', $visits);
            return $this->redirectToRoute('app_stats_visits_results', ['id' => $hive->getId()]);
        }
        return $this->render('stats/visits/search.html.twig', [
            'id' => $hive->getId(),
            'form' => $form,
            'hive' => $hive
        ]);
    }

    #[Route('/hive/{id}/visits/results', name: 'visits_results', methods: ['GET'])]
    public function displayResultVisit(Hive $hive, Request $request): Response
    {
        $this->denyAccessUnlessGranted('own', $hive);
        $visits = $request->getSession()->get('visit_results');
        if (null === $visits) {
            $form = $this->createForm(DatesType::class, null);
            $form->addError(new FormError("Une erreur est survenue, veuillez recommencer"));
            return $this->render('stats/visits/search.html.twig', [
                'id' => $hive->getId(),
                'form' => $form,
                'hive' => $hive
            ]);
        }
        return $this->render('stats/visits/details.html.twig', [
            'hive' => $hive,
            'visits' => $visits['visits'],
            'endDate' => $visits['endDate'],
            'startDate' => $visits['startDate'],
            'doc' => $visits['path']
        ]);
    }

    #[Route('/hive/{id}/temperature', name: 'temps_search', methods: ['GET', 'POST'])]
    public function temps(
        Hive $hive,
        Request $request,
        Temps $temperatures,
        #[CurrentUser] Apiculteur $user,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $form = $this->createForm(DatesType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dates = $form->getData();
            $temps = $temperatures->getTemps($hive, $dates, $user, $this->userFolderRoot);
            $request->getSession()->set('temps_results', $temps);
            return $this->redirectToRoute('app_stats_temps_result', ['id' => $hive->getId()]);
        }
        return $this->render('stats/temps/search.html.twig', [
            'id' => $hive->getId(),
            'form' => $form,
            'hive' => $hive
        ]);
    }

    #[Route('/hive/{id}/temperature/result', name: 'temps_result', methods: ['GET'])]
    public function tempsResult(
        Hive $hive,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $temps = $request->getSession()->get('temps_results');
        if (null === $temps) {
            $form = $this->createForm(DatesType::class, null);
            $form->addError(new FormError("Une erreur est survenue, veuillez recommencer"));
            return $this->render('stats/temps/search.html.twig', [
                'id' => $hive->getId(),
                'form' => $form,
                'hive' => $hive
            ]);
        }
        return $this->render('stats/temps/details.html.twig', [
            'hive' => $hive,
            'endDate' => $temps['endDate'],
            'startDate' => $temps['startDate'],
            'doc' => $temps['path']
        ]);
    }

    #[Route('/hive/{id}/weight', name: 'weight_search', methods: ['GET', 'POST'])]
    public function weight(
        Hive $hive,
        Request $request,
        Weight $weightStats,
        #[CurrentUser] Apiculteur $user,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $form = $this->createForm(DatesType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dates = $form->getData();
            $weight = $weightStats->getWeight($hive, $dates, $user, $this->userFolderRoot);
            $request->getSession()->set('weight_results', $weight);
            return $this->redirectToRoute('app_stats_weight_result', ['id' => $hive->getId()]);
        }
        return $this->render('stats/weight/search.html.twig', [
            'id' => $hive->getId(),
            'form' => $form,
            'hive' => $hive
        ]);
    }

    #[Route('/hive/{id}/weight/result', name: 'weight_result', methods: ['GET'])]
    public function weightResult(
        Hive $hive,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $weight = $request->getSession()->get('weight_results');
        if (null === $weight) {
            $form = $this->createForm(DatesType::class, null);
            $form->addError(new FormError("Une erreur est survenue, veuillez recommencer"));
            return $this->render('stats/weight/search.html.twig', [
                'id' => $hive->getId(),
                'form' => $form,
                'hive' => $hive
            ]);
        }
        return $this->render('stats/weight/details.html.twig', [
            'hive' => $hive,
            'endDate' => $weight['endDate'],
            'startDate' => $weight['startDate'],
            'doc' => $weight['path']
        ]);
    }
}
