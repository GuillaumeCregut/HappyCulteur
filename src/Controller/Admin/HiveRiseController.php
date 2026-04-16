<?php

namespace App\Controller\Admin;

use App\Entity\HiveRise;
use App\Form\HiveRiseType;
use App\Repository\HiveRiseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/hiverise', name: 'app_admin_hive_rise_')]
final class HiveRiseController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(HiveRiseRepository $hiveRiseRepository): Response
    {
        return $this->render('admin/hive_rise/index.html.twig', [
            'hive_rises' => $hiveRiseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hiveRise = new HiveRise();
        $form = $this->createForm(HiveRiseType::class, $hiveRise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hiveRise);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_hive_rise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/hive_rise/new.html.twig', [
            'hive_rise' => $hiveRise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HiveRise $hiveRise, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HiveRiseType::class, $hiveRise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_hive_rise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/hive_rise/edit.html.twig', [
            'hive_rise' => $hiveRise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, HiveRise $hiveRise, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hiveRise->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($hiveRise);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_hive_rise_index', [], Response::HTTP_SEE_OTHER);
    }
}
