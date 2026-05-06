<?php

namespace App\Controller;

use App\Entity\Hive;
use App\Entity\Apiculteur;
use App\Service\ConfigMaker;
use App\Dto\DataloggerConfigDto;
use App\Form\DataloggerLoadType;
use App\Form\DataLoggerCreateType;
use App\Service\DataloggerLoadFile;
use App\Tool\PathMaker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[IsGranted('ROLE_USER')]
#[Route('/datalogger', name: 'app_datalogger_')]
final class DataLoggerController extends AbstractController
{

    public function __construct(private readonly string $userFolderRoot) {}

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
            $file = $maker->makeConfig($hive, $config, $this->userFolderRoot, $user);
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
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $configFile = $hive->getDataloggerName();
        if (null === $configFile) {
            $this->createNotFoundException("La ruche n'a pas de datalogger");
        }
        $config = $maker->loadConfig($hive, $this->userFolderRoot);
        $form =  $this->createForm(DataLoggerCreateType::class, $config);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $version = $this->getParameter('app.datalogger.version');
            $signature = $this->getParameter('app.datalogger.signature');
            $config->version = $version;
            $config->signature = $signature;
            $maker->makeConfig($hive, $config, $this->userFolderRoot, $user);
            return $this->redirectToRoute('app_datalogger_hive', ['id' => $hive->getId()]);
        }
        return $this->render('data_logger/update.html.twig', [
            'hive' => $hive,
            'form' => $form
        ]);
    }

    #[Route('/hive/{id}/load', name: 'load', methods: ['GET', 'POST'])]
    public function loadLogs(
        Hive $hive,
        Request $request,
        DataloggerLoadFile $loader,
        EntityManagerInterface $em,
        #[CurrentUser] Apiculteur $user,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        if (null === $hive->getDataloggerName()) {
            throw new NotFoundHttpException('Cette ruche ne possède pas de datalogger');
        }
        $form = $this->createForm(DataloggerLoadType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**@var UploadedFile $datas */
            $datas = $form->get('file')->getData();
            $version = $this->getParameter('app.datalogger.version');
            $signature = $this->getParameter('app.datalogger.signature');
            $errors = $loader->isFileValid($datas, $version, $signature);
            if (!empty($errors)) {
                $errorsString = implode(', ', $errors);
                $form->get('file')->addError(
                    new FormError("Le fichier téléchargé n'est pas valide : {$errorsString}")
                );
                return $this->render('data_logger/load.html.twig', [
                    'hive' => $hive,
                    'form' => $form
                ]);
            }
            $path = PathMaker::makeDataloggerFilePath($user, $hive, $this->userFolderRoot);
            $newFilename = $hive->getIdentification() . '.bin';
            $newFile = $datas->move($path, $newFilename);
            $datas = $loader->loadDatas($newFile->getPathname(), $hive, $this->userFolderRoot, $user);
            if (false === $datas) {
                $form->get('file')->addError(
                    new FormError("Le fichier ne correspond pas à la ruche selectionnée")
                );
                return $this->render('data_logger/load.html.twig', [
                    'hive' => $hive,
                    'form' => $form
                ]);
            }
            foreach ($datas as $data) {
                $em->persist($data);
            }
            $em->flush();
            $count = count($datas);
            $this->addFlash('success', "{$count} enregistrements enregistrés avec succès.");
            return $this->redirectToRoute('app_datalogger_hive', ['id' => $hive->getId()]);
        }
        return $this->render('data_logger/load.html.twig', [
            'hive' => $hive,
            'form' => $form
        ]);
    }
}
