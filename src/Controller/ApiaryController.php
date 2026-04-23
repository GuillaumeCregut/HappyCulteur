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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[IsGranted('ROLE_USER')]
#[Route('/apiary', name: 'app_apiary_')]
final class ApiaryController extends AbstractController
{
    #[Route('/{id}', name: 'index')]
    public function index(
        #[CurrentUser] Apiculteur $user,
        Apiary $apiary,
    ): Response {
        if($apiary->getBeekeeper()->getId() !== $user->getId()) {
            throw new AccessDeniedHttpException('Access denied.'); 
        }
        return $this->render('apiary/index.html.twig', [
            'apiary' => $apiary
        ]);
    }

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

    #[Route('/info/{id}', name: 'info')]
    public function info(
        #[CurrentUser] Apiculteur $user,
        Apiary $apiary,
    ): Response {
        if($apiary->getBeekeeper()->getId() !== $user->getId()) {
            throw new AccessDeniedHttpException('Access denied.'); 
        }
        return $this->render('apiary/info.html.twig', [
            'apiary' => $apiary
        ]);
    }

    #[Route('/update/{id}', name: 'update')]
    public function update(
        #[CurrentUser] Apiculteur $user,
        Apiary $apiary,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if($apiary->getBeekeeper()->getId() !== $user->getId()) {
            throw new AccessDeniedHttpException('Access denied.'); 
        }
        $form = $this->createForm(ApiaryFormType::class, $apiary);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($apiary);
            $em->flush();
            return $this->redirectToRoute('app_apiary_info',['id' => $apiary->getId()]);
        }
        return $this->render('apiary/update.html.twig', [
            'apiary' => $apiary,
            'form' =>$form
        ]);
    }

    #[Route('/edition/{id}', name: 'edition')]
    public function edition(Apiary $apiary): Response
    {
        return $this->render('apiary/editions/index.html.twig', [
            'apiary' => $apiary
        ]);
    }

}
