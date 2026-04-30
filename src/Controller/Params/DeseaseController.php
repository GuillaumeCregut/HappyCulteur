<?php

namespace App\Controller\Params;

use App\Entity\Desease;
use App\Form\DeseaseType;
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
final class DeseaseController extends AbstractController
{
    #[Route('/params/desease', name: 'params_desease_index')]
    public function index(
        Request $request,
        DeseaseRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $deseases = $repo->findBy([], ['name' => 'ASC']);
        $newDesease = new Desease();
        $form = $this->createForm(DeseaseType::class, $newDesease);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($newDesease);
            $em->flush();
            return $this->redirectToRoute('app_params_desease_index');
        }
        return $this->render('params/desease/index.html.twig', [
            'deseases' => $deseases,
            'form' => $form
        ]);
    }


    #[Route('/param/desease/ajax', name: 'param_disease_add', methods: ['GET', 'POST'])]
    public function addAjax(
        Request $request,
        EntityManagerInterface $em
    ): Response { 
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException('Only for ajax call');
        }
        $newDesease = new Desease();
        $form = $this->createForm(DeseaseType::class, $newDesease);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($newDesease);
            $em->flush();
            return $this->json([
                'id'   => $newDesease->getId(),
                'name' => $newDesease->getName(),
            ]);
        }
        return $this->render('visit/_form_add_disease.html.twig', [
            'form' => $form,
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/desease', name: 'admin_desease_index')]
    public function adminDesease(DeseaseRepository $repo): Response
    {
        $deseases = $repo->findBy([], ['name' => 'ASC']);
        return $this->render('admin/desease/index.html.twig', [
            'deseases' => $deseases,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/desease/new', name: 'admin_desease_add', methods: ['GET', 'POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $desease = new Desease();
        $form = $this->createForm(DeseaseType::class, $desease);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($desease);
            $em->flush();
            return $this->redirectToRoute('app_admin_desease_index');
        }
        return $this->render('admin/desease/new.html.twig', [
            'form' => $form,
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/desease/{id}/edit', name: 'admin_desease_update', methods: ['GET', 'POST'])]
    public function update(
        Request $request,
        Desease $desease,
        EntityManagerInterface $em,
    ): Response {
        $form = $this->createForm(DeseaseType::class, $desease);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($desease);
            $em->flush();
            return $this->redirectToRoute('app_admin_desease_index');
        }
        return $this->render('admin/desease/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/desease/{id}', name: 'admin_desease_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Desease $desease,
        EntityManagerInterface $em,
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $desease->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($desease);
            $em->flush();
        }
        return $this->redirectToRoute('app_admin_desease_index');
    }
}
