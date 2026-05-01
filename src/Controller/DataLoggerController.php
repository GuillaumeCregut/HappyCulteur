<?php

namespace App\Controller;

use App\Entity\Hive;
use App\Entity\Apiculteur;
use App\Service\ConfigMaker;
use App\Dto\DataloggerConfigDto;
use App\Form\DataLoggerCreateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[IsGranted('ROLE_USER')]
#[Route('/datalogger', name: 'app_datalogger_')]
final class DataLoggerController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('data_logger/index.html.twig', [
            'controller_name' => 'DataLoggerController',
        ]);
    }

    #[Route('/hive/{id}', name: 'hive')]
    public function hiveDatalogger(
        Hive $hive,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $config = new DataloggerConfigDto();
        $dataLogger = $hive->getDataloggerName();
        if (null === $dataLogger) {
            $form =  $this->createForm(DataLoggerCreateType::class, $config, [
                'action' => $this->generateUrl('app_datalogger_add_hive', ['id' => $hive->getId()]),
                'method' => 'POST'
            ]);
            return $this->render('data_logger/hive_add_datalogger.html.twig', [
                'hive' => $hive,
                'form' => $form
            ]);
        }
        return $this->render('data_logger/index.html.twig', [
            'hive' => $hive,
        ]);
    }

    #[Route('/hive/{id}/add', name: 'add_hive', methods: ['POST'])]
    public function addHiveDatalogger(
        Hive $hive,
        EntityManagerInterface $em,
        ConfigMaker $maker,
        Request $request,
        #[CurrentUser] Apiculteur $user,
        #[Autowire('%kernel.project_dir%/public/uploads/')] string $uploadDirectory
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $config = new DataloggerConfigDto();
        $form =  $this->createForm(DataLoggerCreateType::class, $config, [
            'action' => $this->generateUrl('app_datalogger_add_hive', ['id' => $hive->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $version = $this->getParameter('app.datalogger.version');
            $signature = $this->getParameter('app.datalogger.signature');
            $config->version = $version;
            $config->signature = $signature;
            $file = $maker->makeConfig($hive, $config, $uploadDirectory, $user);
            $hive->setDataloggerName($file);
            $em->flush();
            return $this->redirectToRoute('app_datalogger_hive', ['id' => $hive->getId()]);
        }
        return $this->render('data_logger/hive_add_datalogger.html.twig', [
            'hive' => $hive,
            'form' => $form
        ]);
    }

    #[Route('/hive/{id}/update', name: 'update_hive', methods: ['GET', 'POST'])]
    public function updateConfig(
        Hive $hive,
        ConfigMaker $maker,
        Request $request,
        #[CurrentUser] Apiculteur $user,
        #[Autowire('%kernel.project_dir%/public/uploads/')] string $uploadDirectory
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $configFile = $hive->getDataloggerName();
        if (null === $configFile) {
            $this->createNotFoundException("La ruche n'a pas de datalogger");
        }
        $config = $maker->loadConfig($hive, $uploadDirectory);
        $form =  $this->createForm(DataLoggerCreateType::class, $config);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $version = $this->getParameter('app.datalogger.version');
            $signature = $this->getParameter('app.datalogger.signature');
            $config->version = $version;
            $config->signature = $signature;
            $maker->makeConfig($hive, $config, $uploadDirectory, $user);
            return $this->redirectToRoute('app_datalogger_hive', ['id' => $hive->getId()]);
        }
        return $this->render('data_logger/update.html.twig', [
            'hive' => $hive,
            'form' => $form
        ]);
    }

}
