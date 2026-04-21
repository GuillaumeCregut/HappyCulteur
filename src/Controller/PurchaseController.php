<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Entity\Apiculteur;
use App\Form\PurchaseType;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/purchase', name: 'app_purchase_')]
final class PurchaseController extends AbstractController
{
    #[Route('', name: 'index', methods:['GET', 'POST'])]
    public function index(
        #[CurrentUser] Apiculteur $user,
        EntityManagerInterface $em,
        Request $request
    ): Response
    {
        $purchases = $user->getPurchases();
        $newPurchase = new Purchase();
        $form = $this->createForm(PurchaseType::class,$newPurchase);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $newPurchase->setBeekeeper($user);
            $em->persist($newPurchase);
            $em->flush();
            return $this->redirectToRoute('app_purchase_index');
        }
        return $this->render('purchase/index.html.twig', [
            'purchases' => $purchases, 
            'form' => $form
        ]);
    }
}
