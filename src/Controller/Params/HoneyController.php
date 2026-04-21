<?php

namespace App\Controller\Params;

use App\Entity\Honey;
use App\Exception\PictureException;
use App\Form\HoneyType;
use App\Service\Uploader;
use App\Repository\HoneyRepository;
use App\Service\PictureFormator;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('', name: 'app_')]
#[IsGranted('ROLE_USER')]
final class HoneyController extends AbstractController
{
    public function __construct(private SluggerInterface $slugger, private Uploader $uploader) {}

    #[Route('/params/honey', name: 'params_honey_index')]
    public function index(
        Request $request,
        HoneyRepository $repo,
        EntityManagerInterface $em,
        #[Autowire('%kernel.project_dir%/public/uploads/')] string $uploadDirectory
    ): Response {
        $honeys = $repo->findBy([], ['name' => 'ASC']);
        $newHoney = new Honey();
        $form = $this->createForm(HoneyType::class, $newHoney);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**@var UploadedFile $hivePicture */
            $picture = $form->get('picture')->getData();
            $picturePath = $this->handleFile($newHoney->getName(), $uploadDirectory, false, $picture);
            $newHoney->setPicture($picturePath);
            $em->persist($newHoney);
            $em->flush();
            return $this->redirectToRoute('app_params_honey_index');
        }
        return $this->render('params/honey/index.html.twig', [
            'honeys' => $honeys,
            'form' => $form
        ]);
    }

    private function handleFile(string $filename, string $rootPath,  bool $remove, ?UploadedFile $file = null): ?string
    {
        if ($remove) {
            $picturePath = $rootPath . 'admin' . DIRECTORY_SEPARATOR .'honeys' . DIRECTORY_SEPARATOR . $filename;
            unlink($picturePath);
            return null;
        }
        $singleFilename = $this->slugger->slug($filename)->lower()->toString();
        $filename = $singleFilename;

        $resultFilename = $this->uploader->storeFile($file, 'admin', 'honeys', $filename);
        try {
            $result = PictureFormator::format($rootPath . $resultFilename, PictureFormator::TO_PNG, 20, 20);
            if(!$result) {
                throw new Exception("Conversion file failed");
            }
        } catch (PictureException $e) {
            throw new Exception("Conversion file failed :{$e->getMessage()}");
        }
        $newFilename = pathinfo($result, PATHINFO_BASENAME);
        return 'admin' . DIRECTORY_SEPARATOR . 'honeys' . DIRECTORY_SEPARATOR . $newFilename;
    }
}
