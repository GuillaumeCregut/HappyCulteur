<?php

namespace App\Controller;

use App\Entity\Apiary;
use App\Entity\Apiculteur;
use App\Form\ApiaryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/apiary', name: 'app_apiary_')]
final class ApiaryController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function add(
        #[CurrentUser] Apiculteur $user,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $apiary = new Apiary();
        $apiary->setIsActive(true);
        $form = $this->createForm(ApiaryFormType::class, $apiary);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $apiary->setBeekeeper($user);
            $em->persist($apiary);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('apiary/add.html.twig', [
            'form' => $form,
        ]);
    }
}
