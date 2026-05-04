<?php

namespace App\Controller;

use App\Entity\Hive;
use App\Form\Stats\DatesType;
use App\Service\Stats\Visits;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/stats', name: 'app_stats_')]
final class StatsController extends AbstractController
{
    #[Route('/hive/{id}', name: 'index')]
    public function index(
        Hive $hive
    ): Response
    {
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
        $form = $this->createForm(DatesType::class,null);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $dates = $form->getData();
            $visits = $visits->getHiveVisits($hive,$dates);
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
        $visits = $request->getSession()->get('visit_results');
        return $this->render('stats/visits/details.html.twig', [
                'hive' => $hive,
                'visits' => $visits['visits'],
                'endDate' => $visits['endDate'],
                'startDate' => $visits['startDate'] 
            ]);
    }
}
