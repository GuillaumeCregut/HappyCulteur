<?php

namespace App\Controller;

use App\Entity\Apiary;
use App\Service\Uploader;
use App\Entity\Apiculteur;
use App\Form\ApiaryFormType;
use App\Form\ApiaryPictureType;
use App\Tool\PathMaker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/apiary', name: 'app_apiary_')]
final class ApiaryController extends AbstractController
{
    #[Route('/{id}', name: 'index')]
    public function index(
        #[CurrentUser] Apiculteur $user,
        Apiary $apiary,
    ): Response {
        if ($apiary->getBeekeeper()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
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
        if ($apiary->getBeekeeper()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
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
        if ($apiary->getBeekeeper()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }
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
    public function edition(Apiary $apiary): Response
    {
        return $this->render('apiary/editions/index.html.twig', [
            'apiary' => $apiary
        ]);
    }

    #[Route('/cartography/{id}', name: 'cartography', methods: ['GET', 'POST'])]
    public function cartography(
        Apiary $apiary,
        Request $request,
        Uploader $uploader,
        #[CurrentUser] Apiculteur $user,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        #[Autowire("%kernel.project_dir%/public/uploads/")] string $uploadDirectory
    ): Response {
        $form = $this->createForm(ApiaryPictureType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**@var UploadedFile $hivePicture */
            $picture = $form->get('picture')->getData();
            $relativePath = PathMaker::makeCartoApiaryPath($user, $apiary, $uploadDirectory);
            $filePath = $this->handlefile($apiary->getIdentification(), $relativePath, $uploadDirectory, $slugger, false, $uploader, $picture);
            $apiary->setLastPicture($filePath);
            $em->persist($apiary);
            $em->flush();
            return $this->redirectToRoute('app_apiary_cartography', ['id' => $apiary->getId()]);
        }
        return $this->render('apiary/carto.html.twig', [
            'apiary' => $apiary,
            'form' => $form
        ]);
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
    ) : ?string {
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
