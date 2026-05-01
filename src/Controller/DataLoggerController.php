<?php

namespace App\Controller;

use App\Entity\Hive;
use App\Entity\Datalogger;
use App\Form\DataLoggerCreateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;

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
        $dataLogger = $hive->getDatalogger();
        if (null === $dataLogger) {
            $form =  $this->createForm(DataLoggerCreateType::class, null, [
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
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $form =  $this->createForm(DataLoggerCreateType::class, null, [
            'action' => $this->generateUrl('app_datalogger_add_hive', ['id' => $hive->getId()]),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           
        }
        return $this->render('data_logger/hive_add_datalogger.html.twig', [
            'hive' => $hive,
            'form' => $form
        ]);
    }
}
