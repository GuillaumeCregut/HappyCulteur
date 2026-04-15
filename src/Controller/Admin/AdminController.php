<?php

namespace App\Controller\Admin;

use App\Entity\Apiculteur;
use App\Repository\ApiculteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'app_admin_')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/user', name: 'user')]
    public function userPage(ApiculteurRepository $repo): Response
    {
        $users = $repo->findAll();
        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
        ]);
    }

     #[Route('/user/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function removeUser(Request $request, Apiculteur $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($user);
            $em->flush();
        }
        return $this->redirectToRoute('app_admin_user');
    }
}
