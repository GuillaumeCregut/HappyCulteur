<?php

namespace App\Controller;

use App\Constant\UserFolder;
use App\Entity\Apiary;
use App\Entity\Apiculteur;
use App\Service\ApiaryDeclaration;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
final class EditionController extends AbstractController
{
    private string $uploadDirectory;

    public function __construct(
        private SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/')] string $uploadDirectory
    ){
        $this->uploadDirectory = $uploadDirectory;
    }

    #[Route('/edition/apiary/{id}', name: 'app_edition_apiary_declaration')]
    public function declaration(Apiary $apiary): Response
    {
        return $this->render('apiary/editions/declaration.html.twig', [
            'apiary' => $apiary
        ]);
    }

    #[Route('/edition/apiary/{id}/print', name: 'app_edition_apiary_declaration_print')]
    public function printDeclaration(
        Apiary $apiary,
        ApiaryDeclaration $declaration,
        #[CurrentUser] Apiculteur $user,
    ): Response {
        $declaration->createdoc($user, $apiary);
        $userPath = $user->getId() . DIRECTORY_SEPARATOR;
        $userPath .= UserFolder::DOCUMENTS;
        $filename = "declaration_{$apiary->getIdentification()}.pdf";
        $declaration->save($this->uploadDirectory . $userPath, $filename);
         return $this->render('apiary/editions/print_declaration.html.twig', [
            'apiary' => $apiary, 
            'file' => $userPath . $filename
        ]);
    }
}
