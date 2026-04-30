<?php

namespace App\Controller\Params;

use App\Entity\Desease;
use App\Form\DiseaseType;
use App\Repository\DeseaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


#[Route('', name: 'app_')]
#[IsGranted('ROLE_USER')]
final class DiseaseController extends AbstractController
{
    #[Route('/params/disease', name: 'params_disease_index')]
    public function index(
        Request $request,
        DeseaseRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $diseases = $repo->findBy([], ['name' => 'ASC']);
        $newDisease = new Desease();
        $form = $this->createForm(DiseaseType::class, $newDisease);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($newDisease);
            $em->flush();
            return $this->redirectToRoute('app_params_disease_index');
        }
        return $this->render('params/disease/index.html.twig', [
            'diseases' => $diseases,
            'form' => $form
        ]);
    }


    #[Route('/param/disease/ajax', name: 'param_disease_add', methods: ['GET', 'POST'])]
    public function addAjax(
        Request $request,
        EntityManagerInterface $em
    ): Response { 
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException('Only for ajax call');
        }
        $newDisease = new Desease();
        $form = $this->createForm(DiseaseType::class, $newDisease);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($newDisease);
            $em->flush();
            return $this->json([
                'id'   => $newDisease->getId(),
                'name' => $newDisease->getName(),
            ]);
        }
        return $this->render('visit/_form_add_disease.html.twig', [
            'form' => $form,
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/disease', name: 'admin_disease_index')]
    public function adminDisease(DeseaseRepository $repo): Response
    {
        $diseases = $repo->findBy([], ['name' => 'ASC']);
        return $this->render('admin/disease/index.html.twig', [
            'diseases' => $diseases,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/disease/new', name: 'admin_disease_add', methods: ['GET', 'POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $disease = new Desease();
        $form = $this->createForm(DiseaseType::class, $disease);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($disease);
            $em->flush();
            return $this->redirectToRoute('app_admin_disease_index');
        }
        return $this->render('admin/disease/new.html.twig', [
            'form' => $form,
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/disease/{id}/edit', name: 'admin_disease_update', methods: ['GET', 'POST'])]
    public function update(
        Request $request,
        Desease $disease,
        EntityManagerInterface $em,
    ): Response {
        $form = $this->createForm(DiseaseType::class, $disease);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($disease);
            $em->flush();
            return $this->redirectToRoute('app_admin_disease_index');
        }
        return $this->render('admin/disease/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/disease/{id}', name: 'admin_disease_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Desease $disease,
        EntityManagerInterface $em,
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $disease->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($disease);
            $em->flush();
        }
        return $this->redirectToRoute('app_admin_disease_index');
    }
}
