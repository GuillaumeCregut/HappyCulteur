<?php

namespace App\Controller;

use App\Entity\Apiculteur;
use App\Form\ApiculteurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user', name: 'app_user_')]
final class UserController extends AbstractController
{
    #[Route('', name: 'index')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function index(#[CurrentUser] Apiculteur $user): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

     #[Route('/update', name: 'update', methods: ['GET','POST'])]
     #[IsGranted('IS_AUTHENTICATED')]
     public function update(#[CurrentUser] Apiculteur $user, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em): Response
     {
        $form = $this->createForm(ApiculteurType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            if(null !== $plainPassword) {
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }

            $em->persist($user);
            $em->flush();
             return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/update.html.twig',[
            'form' => $form,
            'user' => $user
        ]);
     }

}
