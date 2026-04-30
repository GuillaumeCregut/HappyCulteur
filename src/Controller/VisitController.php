<?php

namespace App\Controller;

use App\Entity\Hive;
use App\Entity\Visit;
use App\Form\VisitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/visit', name: 'app_visit_')]
final class VisitController extends AbstractController
{
    #[Route('/{id}/add', name: 'add')]
    public function index(
        Hive $hive,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $visit = new Visit();
        $form = $this->createForm(VisitType::class, $visit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(!$visit->isDisease()) {
                $visit->setDisease(null);
            }
            if(!$visit->isFeeded()) {
                $visit->setFeeding(null);
            }
            $visit->setHive($hive);
            $em->persist($visit);
            $em->flush();
            return $this->redirectToRoute('app_hive_index', ['id' => $hive->getId()]);
        }
        return $this->render('visit/add.html.twig', [
            'hive' => $hive,
            'form' => $form
        ]);
    }

}
