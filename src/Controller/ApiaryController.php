<?php

namespace App\Controller;

use App\Entity\Apiary;
use App\Tool\PathMaker;
use App\Service\Uploader;
use App\Entity\Apiculteur;
use App\Form\ApiaryFormType;
use App\Form\ApiaryPictureType;
use App\Security\Voter\ApiaryVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/apiary', name: 'app_apiary_')]
final class ApiaryController extends AbstractController
{

    public function __construct(private readonly string $userFolderRoot) {}

    #[Route('/{id}', name: 'index')]
    #[IsGranted(ApiaryVoter::BELONG, subject:'apiary')]
    public function index(
        Apiary $apiary,
    ): Response {
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
            $apiary->setOwner($user);
            $em->persist($apiary);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('apiary/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/info/{id}', name: 'info')]
    #[IsGranted(ApiaryVoter::BELONG, subject:'apiary')]
    public function info(
        Apiary $apiary,
    ): Response {
        return $this->render('apiary/info.html.twig', [
            'apiary' => $apiary
        ]);
    }

    #[Route('/update/{id}', name: 'update')]
    #[IsGranted(ApiaryVoter::OWN, subject:'apiary')]
    public function update(
        Apiary $apiary,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(ApiaryFormType::class, $apiary);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($apiary);
            $em->flush();
            return $this->redirectToRoute('app_apiary_info', ['id' => $apiary->getId()]);
        }
        return $this->render('apiary/update.html.twig', [
            'apiary' => $apiary,
            'form' => $form
        ]);
    }

    #[Route('/edition/{id}', name: 'edition')]
    #[IsGranted(ApiaryVoter::OWN, subject:'apiary')]
    public function edition(Apiary $apiary, #[CurrentUser] Apiculteur $user): Response
    {
        return $this->render('apiary/editions/index.html.twig', [
            'apiary' => $apiary
        ]);
    }

    #[Route('/cartography/{id}', name: 'cartography', methods: ['GET', 'POST'])]
    #[IsGranted(ApiaryVoter::OWN, subject:'apiary')]
    public function cartography(
        Apiary $apiary,
        Request $request,
        Uploader $uploader,
        #[CurrentUser] Apiculteur $user,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
    ): Response {
        $form = $this->createForm(ApiaryPictureType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**@var UploadedFile $hivePicture */
            $picture = $form->get('picture')->getData();
            $relativePath = PathMaker::makeCartoApiaryPath($user, $apiary, $this->userFolderRoot);
            $filePath = $this->handlefile($apiary->getIdentification(), $relativePath, $this->userFolderRoot, $slugger, false, $uploader, $picture);
            $apiary->setPathImage($filePath);
            $em->persist($apiary);
            $em->flush();
            return $this->redirectToRoute('app_apiary_cartography', ['id' => $apiary->getId()]);
        }
        return $this->render('apiary/carto.html.twig', [
            'apiary' => $apiary,
            'form' => $form
        ]);
    }

    #[Route('/cartography/{id}/see', name: 'see_carto')]
    #[IsGranted(ApiaryVoter::BELONG, subject:'apiary')]
    public function seeApiaryCarto(Apiary $apiary): Response
    {
        return $this->render('apiary/carto_see.html.twig', [
            'apiary' => $apiary,

        ]);
    }

    #[Route('/cartography/save/{id}', name: 'save_carto', methods: ['POST'])]
    #[IsGranted(ApiaryVoter::OWN, subject:'apiary')]
    public function saveApiaryCarto(
        Request $request,
        Apiary $apiary,
        CsrfTokenManagerInterface $csrf,
        SluggerInterface $slugger,
        EntityManagerInterface $em,
    ): Response {
        $token = $request->headers->get('X-CSRF-Token');
        if (!$csrf->isTokenValid(new CsrfToken('save_coords', $token))) {
            return $this->json(['error' => 'Invalid CSRF token'], 403);
        }
        /**@var UploadedFile $file */
        $file = $request->files->get('image');
        if (null === $file) {
            return $this->json(['error' => 'File is empty'], 422);
        }
        $relativePath = PathMaker::makeApiaryFullCartoPath($apiary, $this->userFolderRoot);
        $fullPath = $this->userFolderRoot . $relativePath;
        $filename = $slugger->slug($apiary->getIdentification())->lower()->toString();
        $filename .= '.png';
        $file->move($fullPath, $filename);
        $apiary->setLastPicture($relativePath . $filename);
        $em->flush();
        return $this->json(['success' => 'success'], 200);
    }

    /**
     * Will update or remove cartography file 
     * If remove, return null else return relative path and filename with exetension
     *
     * @param string $filename
     * @param string $relativePath
     * @param string $rootPath
     * @param SluggerInterface $slugger
     * @param boolean $remove
     * @param Uploader $uploader
     * @param UploadedFile|null $file
     * @return string|null
     */
    private function handlefile(
        string $filename,
        string $relativePath,
        string $rootPath,
        SluggerInterface $slugger,
        bool $remove,
        Uploader $uploader,
        ?UploadedFile $file = null
    ): ?string {
        if ($remove) {
            $picturePath = $rootPath . $filename;
            unlink($picturePath);
            return null;
        }
        $filename = $slugger->slug($filename)->lower()->toString();
        $fullPath = $rootPath . $relativePath;
        $resultFilename = $uploader->storeFile($file, $fullPath, $filename);
        return $relativePath . $resultFilename;
    }
}
