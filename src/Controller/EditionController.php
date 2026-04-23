<?php

namespace App\Controller;

use App\Entity\Apiary;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
final class EditionController extends AbstractController
{
    #[Route('/edition/apiary/{id}', name: 'app_edition_apiary_declaration')]
    public function index(Apiary $apiary): Response
    {
        return $this->render('apiary/editions/declaration.html.twig', [
            'apiary' => $apiary
        ]);
    }
}
