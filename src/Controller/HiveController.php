<?php

namespace App\Controller;

use App\Entity\Hive;
use App\Entity\Apiary;
use App\Form\HiveType;
use App\Entity\Apiculteur;
use App\Service\HiveProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[IsGranted('ROLE_USER')]
#[Route('/hive', name: 'app_hive_')]
final class HiveController extends AbstractController
{
    #[Route('/{id}', name: 'index')]
    public function index(Hive $hive): Response
    {
        $this->denyAccessUnlessGranted('own', $hive);
        return $this->render('hive/index.html.twig', [
            'hive' => $hive
        ]);
    }

    #[Route('/add/apiary/{id}', name: 'add')]
    public function add(
        #[CurrentUser] Apiculteur $user,
        EntityManagerInterface $em,
        Apiary $apiary,
        Request $request
    ): Response {
        if ($apiary->getBeekeeper() !== $user) {
            throw new AccessDeniedHttpException('Access denied.');
        }
        $hive = new Hive();
        $form = $this->createForm(HiveType::class, $hive);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hive->setApiary($apiary);
            $qrCode = HiveProcessor::generateQR($user, $hive);
            $hive->setQrCode($qrCode);
            $em->persist($hive);
            $em->flush();
            return $this->redirectToRoute('app_apiary_index', ['id' => $apiary->getId()]);
        }
        return $this->render('hive/add.html.twig', [
            'apiary' => $apiary,
            'form' => $form
        ]);
    }

    #[Route('/{id}/informations', name: 'infos')]
    public function informations(Hive $hive): Response
    {
        $this->denyAccessUnlessGranted('own', $hive);
        return $this->render('hive/info.html.twig', [
            'hive' => $hive
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function update(
        Hive $hive,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $form = $this->createForm(HiveType::class, $hive);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_hive_infos', ['id' => $hive->getId()]);
        }
        return $this->render('hive/update.html.twig', [
            'hive' => $hive,
            'form' => $form
        ]);
    }

    #[Route('/{id}/qrCode', name: 'qrCode', methods: ['GET'])]
    public function generateQrCode(
        Hive $hive,
        Request $request,
        #[CurrentUser] Apiculteur $user,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        if (null === $hive->getQrCode()) {
            $qrCode = HiveProcessor::generateQR($user, $hive);
            $hive->setQrCode($qrCode);
            $em->flush();
        }
        $width  = $request->query->getInt('width', 210);
        $fontPath = 'src'. DIRECTORY_SEPARATOR . 'tool' . DIRECTORY_SEPARATOR .'arial.ttf';
        $font = $this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . $fontPath;
        $picture  = HiveProcessor::generatePoster($hive, $font, $width);
        ob_start();
        imagepng($picture);
        $imageData = ob_get_clean();
        return new Response($imageData, 200, [
        'Content-Type' => 'image/png',
    ]);
    }
}
