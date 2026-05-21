<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/help', name: 'app_help_')]
final class HelpController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('help.html.twig', []);
    }

     #[Route('/home', name: 'home')]
    public function home(): Response
    {
        return $this->render('home/help.html.twig', []);
    }

    #[Route('/apiary', name: 'apiary')]
    public function apiary(): Response
    {
        return $this->render('apiary/help.html.twig', [
           
        ]);
    }

    #[Route('/hive', name: 'hive')]
    public function hive(): Response
    {
        return $this->render('hive/help.html.twig', [
           
        ]);
    }
}
