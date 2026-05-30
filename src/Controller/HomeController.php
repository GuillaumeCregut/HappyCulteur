<?php

namespace App\Controller;

use App\Entity\Apiculteur;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/', name: 'app_')]
final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        AuthenticationUtils $authenticationUtils,
    ): Response {
        $value = $this->getParameter('app.site_name');
        $error = $authenticationUtils->getLastAuthenticationError();
        $user = $this->getUser();
        /**@var Apiculteur $user */
        if ($user) {
            $apiaries = $user->getApiaries();
            $sharedAparies = $user->getSharedApiaries();
        } else {
            $apiaries = [];
            $sharedAparies = [];
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('home/index.html.twig', [
            'value' => $value,
            'last_username' => $lastUsername,
            'error' => $error,
            'apiaries' => $apiaries,
            'shared' => $sharedAparies
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

    #[Route(path: '/params', name: 'params')]
    #[IsGranted('ROLE_USER')]
    public function parameters(): Response
    {
        return $this->render('params/index.html.twig', []);
    }

    #[Route(path: '/params/infos', name: 'params_infos')]
    #[IsGranted('ROLE_USER')]
    public function parametersInfo(): Response
    {
        $version = $this->getParameter('app.datalogger.version');
        return $this->render('params/infos_system.html.twig', ['version' => $version]);
    }

    #[Route(path: '/params/appearence', name: 'params_appearence')]
    #[IsGranted('ROLE_USER')]
    public function appearence(): Response
    {
        return $this->render('params/appearence.html.twig', []);
    }
}
