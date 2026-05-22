<?php

namespace App\Controller;

use App\Entity\Apiculteur;
use App\Repository\HiveRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/stock', name: 'app_stock_')]
final class StockController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(
        #[CurrentUser] Apiculteur $user,
        HiveRepository $repo,
    ): Response {
        $hives = $repo->findByStock($user);
        return $this->render('stock/index.html.twig', [
            'hives' => $hives
        ]);
    }
}
