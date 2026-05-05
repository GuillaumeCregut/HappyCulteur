<?php

namespace App\Controller\Admin;

use App\Constant\HiveState;
use App\Entity\HiveKind;
use App\Form\HiveKindType;
use App\Repository\HiveKindRepository;
use App\Service\HivePictureCreator;
use App\Service\Uploader;
use App\Tool\PathMaker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/hivekind', name: 'app_admin_hive_kind_')]
final class HiveKindController extends AbstractController
{

    public function __construct(
        private Uploader $uploader,
        private HivePictureCreator $pictureCreator,
        private SluggerInterface $slugger
    ) {}

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(HiveKindRepository $hiveKindRepository): Response
    {
        return $this->render('admin/hive_kind/index.html.twig', [
            'hive_kinds' => $hiveKindRepository->findBy([], ['name' => 'ASC']),
            'active' => HiveState::ACTIVE_HIVE->label(),
            'stock' => HiveState::STOCK_HIVE->label(),
            'dead' => HiveState::DEAD_HIVE->label()
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%/public/uploads/')] string $uploadDirectory
    ): Response {
        $hiveKind = new HiveKind();
        $form = $this->createForm(HiveKindType::class, $hiveKind);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**@var UploadedFile $hivePicture */
            $hivePicture = $form->get('picture')->getData();
            $relativePath = PathMaker::makeAdminHivesFolder($uploadDirectory);
            $filename = $this->handleFile($hiveKind->getName(), $relativePath, $uploadDirectory, false, $hivePicture);
            $hiveKind->setPicture($filename);
            $entityManager->persist($hiveKind);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_hive_kind_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/hive_kind/new.html.twig', [
            'hive_kind' => $hiveKind,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        HiveKind $hiveKind,
        EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%/public/uploads/')] string $uploadDirectory
    ): Response {
        $form = $this->createForm(HiveKindType::class, $hiveKind, [
            'is_edit' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hivePicture = $form->get('picture')->getData();
            if (null !== $hivePicture) {
                $relativePath = PathMaker::makeAdminHivesFolder($uploadDirectory);
                $filename = $this->handleFile($hiveKind->getName(), $relativePath, $uploadDirectory, false, $hivePicture);
                $hiveKind->setPicture($filename);
            }
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_hive_kind_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/hive_kind/edit.html.twig', [
            'hive_kind' => $hiveKind,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(
        Request $request,
        HiveKind $hiveKind,
        EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%/public/uploads/')] string $uploadDirectory
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $hiveKind->getId(), $request->getPayload()->getString('_token'))) {
            $this->handleFile($hiveKind->getPicture(), '', $uploadDirectory, true);
            $entityManager->remove($hiveKind);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_hive_kind_index', [], Response::HTTP_SEE_OTHER);
    }

    private function handleFile(
        string $filename,
        string $relativePath,
        string $rootPath,
        bool $remove,
        ?UploadedFile $file = null
    ): ?string {
        if ($remove) {
            $picturePath = $rootPath . DIRECTORY_SEPARATOR . $filename;
            $files = [
                $picturePath . HiveState::ACTIVE_HIVE->label() . '.png',
                $picturePath . HiveState::STOCK_HIVE->label() . '.png',
                $picturePath . HiveState::DEAD_HIVE->label() . '.png'
            ];
            foreach ($files as $filePicture) {
                unlink($filePicture);
            }
            return null;
        }
        $baseFileName =  $this->slugger->slug($filename)->lower()->toString();
        $filename = $baseFileName . HiveState::ACTIVE_HIVE->label();
        $ext =  $file->guessExtension();
        $fullPath = $rootPath . $relativePath;
        $resultFilename = $this->uploader->storeFile($file, $fullPath, $filename);
        $stockFilename =  $baseFileName . HiveState::STOCK_HIVE->label() . '.' . $ext;
        $this->pictureCreator->convert($fullPath . $resultFilename,  $fullPath . $stockFilename, HivePictureCreator::B_AND_WHITE);
        $deadFilename =  $baseFileName . HiveState::DEAD_HIVE->label() . '.' . $ext;
        $this->pictureCreator->convert($fullPath . $resultFilename,  $fullPath . $deadFilename, HivePictureCreator::DEAD);
        return $relativePath . $baseFileName;
    }
}
