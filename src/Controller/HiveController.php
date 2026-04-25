<?php

namespace App\Controller;

use App\Entity\Hive;
use App\Entity\Apiary;
use App\Form\HiveType;
use App\Entity\HiveKind;
use App\Entity\HiveRise;
use App\Entity\Apiculteur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[IsGranted('ROLE_USER')]
#[Route('/hive', name: 'app_hive_')]
final class HiveController extends AbstractController
{
    #[Route('/{id}', name: 'index')]
    public function index(Hive $hive): Response
    {
        $this->denyAccessUnlessGranted('own', $hive);
        return $this->render('hive/index.html.twig', [
            'hive' => $hive
        ]);
    }

    #[Route('/add/apiary/{id}', name: 'add')]
    public function add(
        #[CurrentUser] Apiculteur $user,
        EntityManagerInterface $em,
        Apiary $apiary,
        Request $request
    ): Response {
        if ($apiary->getBeekeeper() !== $user) {
            throw new AccessDeniedHttpException('Access denied.');
        }
        $hive = new Hive();
        $form = $this->createForm(HiveType::class, $hive);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hive->setApiary($apiary);
            $em->persist($hive);
            $em->flush();
            return $this->redirectToRoute('app_apiary_index', ['id' => $apiary->getId()]);
        }
        return $this->render('hive/add.html.twig', [
            'apiary' => $apiary,
            'form' => $form
        ]);
    }

    #[Route('/{id}/informations', name: 'infos')]
    public function informations(Hive $hive): Response
    {
        $this->denyAccessUnlessGranted('own', $hive);
        return $this->render('hive/info.html.twig', [
            'hive' => $hive
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function update(
        Hive $hive,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $form = $this->createForm(HiveType::class, $hive);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_hive_info', ['id' => $hive->getId()]);
        }
        return $this->render('hive/update.html.twig', [
            'hive' => $hive,
            'form' => $form
        ]);
    }
}
