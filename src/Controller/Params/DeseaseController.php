<?php

namespace App\Controller\Params;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

 #[Route('/params/desease', name: 'app_params_desease_')]
 #[IsGranted('ROLE_USER')]
final class DeseaseController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('params/desease/index.html.twig', [
            
        ]);
    }
}
