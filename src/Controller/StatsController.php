<?php

namespace App\Controller;

use App\Entity\Hive;
use App\Entity\Apiary;
use App\Entity\Apiculteur;
use App\Service\Stats\Temps;
use App\Form\Stats\DatesType;
use App\Service\Stats\Visits;
use App\Service\Stats\Weight;
use App\Service\Stats\Harvest;
use App\Dto\DataloggerStatsDto;
use App\Service\Stats\Beekeeper;
use App\Service\Stats\Datalogger;
use App\Service\Stats\Hygrometry;
use App\Service\Stats\HiveResults;
use Symfony\Component\Form\FormError;
use App\Service\Stats\Apiary as StatsApiary;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
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

    #[Route('/hive/{id}/hygrometry', name: 'hygrometry_search', methods: ['GET', 'POST'])]
    public function hygrometry(
        Hive $hive,
        Request $request,
        Hygrometry $hygro,
        #[CurrentUser] Apiculteur $user,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $form = $this->createForm(DatesType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dates = $form->getData();
            $hygrometry = $hygro->gethygrometry($hive, $dates, $user, $this->userFolderRoot);
            $request->getSession()->set('hygrometry_results', $hygrometry);
            return $this->redirectToRoute('app_stats_hygrometry_result', ['id' => $hive->getId()]);
        }
        return $this->render('stats/hygrometry/search.html.twig', [
            'id' => $hive->getId(),
            'form' => $form,
            'hive' => $hive
        ]);
    }

    #[Route('/hive/{id}/hygrometry/result', name: 'hygrometry_result', methods: ['GET'])]
    public function hygroResult(
        Hive $hive,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $hygrometry = $request->getSession()->get('hygrometry_results');
        if (null === $hygrometry) {
            $form = $this->createForm(DatesType::class, null);
            $form->addError(new FormError("Une erreur est survenue, veuillez recommencer"));
            return $this->render('stats/hygrometry/search.html.twig', [
                'id' => $hive->getId(),
                'form' => $form,
                'hive' => $hive
            ]);
        }
        return $this->render('stats/hygrometry/details.html.twig', [
            'hive' => $hive,
            'endDate' => $hygrometry['endDate'],
            'startDate' => $hygrometry['startDate'],
            'doc' => $hygrometry['path']
        ]);
    }

    #[Route('/hive/{id}/datalogger', name: 'datalogger_search', methods: ['GET', 'POST'])]
    public function datalogger(
        Hive $hive,
        Datalogger $dataloggerStats,
        Request $request,
        #[CurrentUser] Apiculteur $user,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $form = $this->createForm(DatesType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dates = $form->getData();
            $stats = $dataloggerStats->getLogs($hive, $dates, $user, $this->userFolderRoot);
            $request->getSession()->set('datalogger_results', $stats);
            return $this->redirectToRoute('app_stats_datalogger_result', ['id' => $hive->getId()]);
        }
        return $this->render('stats/datalogger/search.html.twig', [
            'hive' => $hive,
            'form' => $form
        ]);
    }

    #[Route('/hive/{id}/datalogger/result', name: 'datalogger_result', methods: ['GET'])]
    public function dataloggerResult(
        Hive $hive,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        /**@var DataloggerStatsDto $datas */
        $datas =  $request->getSession()->get('datalogger_results', null);
        if (null === $datas) {
            $form = $this->createForm(DatesType::class, null);
            $form->addError(new FormError("Une erreur est survenue, veuillez recommencer"));
            return $this->render('stats/datalogger/search.html.twig', [
                'form' => $form,
                'hive' => $hive
            ]);
        }
        return $this->render('stats/datalogger/details.html.twig', [
            'hive' => $hive,
            'datas' => $datas,
        ]);
    }

    #[Route('/hive/{id}/harvest', name: 'harvest_search', methods: ['GET', 'POST'])]
    public function harvest(
        Hive $hive,
        Request $request,
        #[CurrentUser] Apiculteur $user,
        Harvest $finder,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $form = $this->createForm(DatesType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dates = $form->getData();
            $harvests = $finder->getHarvest($dates, $user, $hive, $this->userFolderRoot);
            $request->getSession()->set('harvests_results', $harvests);
            return $this->redirectToRoute('app_stats_harvest_result', ['id' => $hive->getId()]);
        }
        return $this->render('stats/harvest/search.html.twig', [
            'hive' => $hive,
            'form' => $form
        ]);
    }
    #[Route('/hive/{id}/harvest/result', name: 'harvest_result', methods: ['GET'])]
    public function harvestResult(
        Request $request,
        Hive $hive
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $datas =  $request->getSession()->get('harvests_results', null);
        if (null === $datas) {
            $form = $this->createForm(DatesType::class, null);
            $form->addError(new FormError("Une erreur est survenue, veuillez recommencer"));
            return $this->render('stats/harvest/search.html.twig', [
                'form' => $form,
                'hive' => $hive
            ]);
        }
        return $this->render('stats/harvest/details.html.twig', [
            'hive' => $hive,
            'datas' => $datas,
        ]);
    }

    #[Route('/hive/{id}/results', name: 'hive_results', methods: ['GET'])]
    public function hiveResults(
        Hive $hive,
        Request $request,
        #[CurrentUser] Apiculteur $user,
        HiveResults $results,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        //create pdf
        $path = $results->getHiveStats($hive, $user, $this->userFolderRoot);
        $request->getSession()->set('hive_stats_results', $path);
        return $this->render('stats/results/index.html.twig', [
            'hive' => $hive,
        ]);
    }

    #[Route('/apiary/{id}/results', name: 'apiary_results')]
    public function apiaryResult(
        Apiary $apiary,
        Request $request,
        #[CurrentUser] Apiculteur $user,
        StatsApiary $statsApiary,
    ): Response {
        if ($apiary->getBeekeeper()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }
        $stats = $statsApiary->getApiaryStats($apiary, $this->userFolderRoot);
        $request->getSession()->set('apiary_stats_results', $stats['path']);
        return $this->render('stats/apiary/index.html.twig', [
            'apiary' => $apiary,
            'stats' => $stats
        ]);
    }

    #[Route('/beekeeper', name: 'beekeeper_home', methods: ['GET'])]
    public function statsBeekeeper(
        #[CurrentUser] Apiculteur $user,
        Beekeeper $beekeeperStats,
    ): Response {
        $stats = $beekeeperStats->getBeekeeperStats($user);
        return $this->render('stats/beekeeper/index.html.twig', [
            'apiaries' => $stats['apiaries'],
            'harvests' => $stats['harvests'],
            'apiariesList' => $user->getApiaries()
        ]);
    }
}
