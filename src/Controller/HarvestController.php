<?php

namespace App\Controller;

use App\Entity\Hive;
use App\Entity\Harvest;
use App\Form\HarvestType;
use App\Entity\Apiculteur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/harvest', name: 'app_harvest_')]
final class HarvestController extends AbstractController
{
    #[Route('/add/hive/{id}', name: 'add', methods:['GET', 'POST'])]
    public function index(
        Request $request,
        #[CurrentUser] Apiculteur $user,
        Hive $hive, 
        EntityManagerInterface $em
    ): Response
    {
        $this->denyAccessUnlessGranted('own', $hive);
        $harvest = new Harvest();
        $form = $this->createForm(HarvestType::class, $harvest);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $harvest->setBeekeeper($user);
            $harvest->setHive($hive);    
            $em->persist($harvest);
            $em->flush();
            $this->addFlash('success', 'Récolte enregistrée avec succès');
            return $this->redirectToRoute('app_hive_index',['id' => $hive->getId()]);
        }
        return $this->render('harvest/add.html.twig', [
            'hive' => $hive,
            'form' => $form   
        ]);
    }
}
