<?php

namespace App\Controller\Params;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    }
}
