<?php

namespace App\Controller;

use App\Dto\ApiaryDto;
use App\Entity\Hive;
use App\Entity\Apiary;
use App\Form\HiveType;
use App\Entity\Apiculteur;
use App\Form\HiveTransferType;
use App\Repository\ApiaryRepository;
use App\Repository\HiveRepository;
use App\Service\HiveFinder;
use App\Service\HiveProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

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

    #[Route('/{id}/translate', name: 'translate', methods: ['GET', 'POST'])]
    public function translate(
        Hive $hive,
        EntityManagerInterface $em,
        #[CurrentUser] Apiculteur $user,
        ApiaryRepository $repo,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $apiaries = $repo->findByBeekeeper($user);
        $dtos = [];
        foreach ($apiaries as $apiary) {
            if ($hive->getApiary() !== $apiary) {
                $dtos[] = new ApiaryDto($apiary->getId(), $apiary->getName());
            }
        }
        $form = $this->createForm(HiveTransferType::class, null, ['dtos' =>  $dtos]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newId = $form->get('apiaryList')->getNormData();
            $newApiary = $repo->findOneBy(['id' => $newId]);
            $hive->setApiary($newApiary);
            $hive->setCoordX(null);
            $hive->setCoordY(null);
            $hive->setCoordZ(null);
            $em->flush();
            return $this->redirectToRoute('app_hive_index', ['id' => $hive->getId()]);
        }
        return $this->render('hive/translate.html.twig', [
            'hive' => $hive,
            'form' => $form
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
        $fontPath = 'src' . DIRECTORY_SEPARATOR . 'tool' . DIRECTORY_SEPARATOR . 'arial.ttf';
        $font = $this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . $fontPath;
        $picture  = HiveProcessor::generatePoster($hive, $font, $width);
        ob_start();
        imagepng($picture);
        $imageData = ob_get_clean();
        return new Response($imageData, 200, [
            'Content-Type' => 'image/png',
        ]);
    }

    #[Route('/{id}/carto', name: 'carto', methods: ['GET'])]
    public function carto(Hive $hive): Response
    {
        $this->denyAccessUnlessGranted('own', $hive);
        return $this->render('hive/carto.html.twig', [
            'hive' => $hive,
        ]);
    }

    #[Route('/{id}/carto/save', name: 'carto_save', methods: ['POST'])]
    public function cartoSave(
        Hive $hive,
        Request $request,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrf,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $token = $request->headers->get('X-CSRF-Token');
        if (!$csrf->isTokenValid(new CsrfToken('save_coords', $token))) {
            return $this->json(['error' => 'Invalid CSRF token'], 403);
        }
        $data = json_decode($request->getContent(), true);
        $hive->setCoordX($data['x'])
            ->setCoordY($data['y'])
            ->setCoordZ($data['z']);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/apiary/{id}', name: 'by_apiary', methods: ['GET'])]
    public function getHivesByApiary(
        Apiary $apiary,
        #[CurrentUser] Apiculteur $user,
        HiveRepository $repo
    ): Response {
        if ($apiary->getBeekeeper()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }
        $hives = $repo->findByApiary($apiary);
        return $this->json($hives);
    }

    #[Route('/beekeeper/stats', name: 'beepkeeper_stats', methods: ['GET'])]
    public function getBeekeeperApiaries(
        #[CurrentUser] Apiculteur $user,
        ApiaryRepository $repo,
        HiveFinder $finder,
        Request $request
    ): Response {
        $apiaryId = $request->query->getInt('apiaryId');
        if (0 === $apiaryId) {
            return $this->json(['error' => 'no apiary sent'], 422);
        }
        $apiary = $repo->findOneBy(['id' => $apiaryId]);
        if (null === $apiary) {
            return $this->json(['error' => 'no apiary found'], 404);
        }
        $hives = $finder->findhiveByApiaryOwnedByUser($apiary, $user);
        return $this->json($hives, 200);
    }

    #[Route('/beekeeper/resume', name: 'beekeeper_resume', methods: ['GET'])]
    public function getHiveResume(
        #[CurrentUser] Apiculteur $user,
        HiveFinder $finder,
        Request $request
    ): Response {
        $hiveId = $request->query->getInt('hiveId');
        if (0 === $hiveId) {
            return $this->json(['error' => 'no hive sent'], 422);
        }
        $hiveInfo = $finder->findHiveByIdOwnedByUser($hiveId, $user);
        if (200 !== $hiveInfo['error']) {
            return $this->json(['error' => 'something went wrong'], $hiveInfo['error']);
        }
        return $this->json([
            'state' => $hiveInfo['state'],
            'rise' => $hiveInfo['rise'],
            'swarm' => $hiveInfo['swarm'],
        ], 200);
    }
}
