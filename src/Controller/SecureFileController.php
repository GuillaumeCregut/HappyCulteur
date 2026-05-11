<?php

namespace App\Controller;

use App\Entity\Hive;
use App\Entity\Apiary;
use App\Tool\PathMaker;
use App\Entity\Apiculteur;
use App\Dto\DataloggerStatsDto;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/carto/apiary/{id}', name: 'carto_apiary')]
    public function apiaryCarto(
        Apiary $apiary,
        #[CurrentUser] Apiculteur $user,
    ): Response {
        if ($user !== $apiary->getBeekeeper()) {
            throw $this->createAccessDeniedException();
        }
        $filename = $this->userFolderRoot . $apiary->getPathImage();
        return $this->file($filename);
    }

    #[Route('/stats/visits/hive/{id}', name: 'stats_visit_hive')]
    public function visitStats(
        Hive $hive,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $visits = $request->getSession()->get('visit_results');
        $doc = $visits['path'];
        $filename = $this->userFolderRoot . $doc;
        return $this->file($filename);
    }

    #[Route('/stats/temperature/hive/{id}', name: 'stats_temp_hive')]
    public function tempStats(
        Hive $hive,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $temp = $request->getSession()->get('temps_results');
        $doc = $temp['path'];
        $filename = $this->userFolderRoot . $doc;
        return $this->file($filename);
    }

    #[Route('/stats/weight/hive/{id}', name: 'stats_weight_hive')]
    public function weightStats(
        Hive $hive,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $weight = $request->getSession()->get('weight_results');
        $doc = $weight['path'];
        $filename = $this->userFolderRoot . $doc;
        return $this->file($filename);
    }

    #[Route('/stats/hygrometry/hive/{id}', name: 'stats_hygrometry_hive')]
    public function hygrometryStats(
        Hive $hive,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $hygrometry = $request->getSession()->get('hygrometry_results');
        $doc = $hygrometry['path'];
        $filename = $this->userFolderRoot . $doc;
        return $this->file($filename);
    }

    #[Route('/stats/datalogger/hive/{id}/{kind}', name: 'stats_datalogger_hive')]
    public function dataloggerStats(
        Hive $hive,
        Request $request,
        string $kind,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        /**@var DataloggerStatsDto $datas */
        $datas = $request->getSession()->get('datalogger_results');
        switch ($kind) {
            case 'temp':
                $doc = $datas->graphTemp;
                break;
            case 'hygro':
                $doc = $datas->graphHygro;
                break;
            case 'weight':
                $doc = $datas->graphWeight;
                break;
            default:
                $doc = null;
        }
        if (null === $doc) {
            throw  $this->createNotFoundException();
        }
        $filename = $this->userFolderRoot . $doc;
        return $this->file($filename);
    }

    #[Route('/stats/harvest/hive/{id}', name: 'stats_harvest_file')]
    public function harvestStats(
        Hive $hive,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $data = $request->getSession()->get('harvests_results');
        $filename = $this->userFolderRoot . $data->file;
        return $this->file($filename);
    }
}
