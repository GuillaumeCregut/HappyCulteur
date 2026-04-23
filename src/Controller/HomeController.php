<?php

namespace App\Controller;

use App\Entity\Apiculteur;
use App\Service\UserConnected;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/', name: 'app_')]
final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        AuthenticationUtils $authenticationUtils, 
         #[Autowire('%kernel.project_dir%/public/uploads/')] string $uploadDirectory,
        UserConnected $tools): Response
    {
        $value = $this->getParameter('app.site_name');
        $error = $authenticationUtils->getLastAuthenticationError();
        $user = $this->getUser();
        /**@var Apiculteur $user */
        if($user) {
            $apiaries = $user->getApiaries();
             //TODO: cleanup user folder
             $tools->cleanUp($user, $uploadDirectory);
        } else {
            $apiaries = [];
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('home/index.html.twig', [
            'value' => $value,
            'last_username' => $lastUsername,
            'error' => $error,
            'apiaries' => $apiaries
        ]);
        
    }

    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $value = $this->getParameter('app.site_name');
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('home/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'value' => $value
        ]);

    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path:'/params', name: 'params')]
    #[IsGranted('ROLE_USER')]
    public function parameters(): Response
    {
         return $this->render('params/index.html.twig', [
            
        ]);
    }
}
