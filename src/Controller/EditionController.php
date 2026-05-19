<?php

namespace App\Controller;

use App\Entity\Apiary;
use App\Entity\Apiculteur;
use App\Service\ApiaryDeclaration;
use App\Tool\PathMaker;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
final class EditionController extends AbstractController
{

    public function __construct(
        private SluggerInterface $slugger,
        private readonly string $userFolderRoot
    ) {}

    #[Route('/edition/apiary/{id}', name: 'app_edition_apiary_declaration')]
    public function declaration(Apiary $apiary, #[CurrentUser] Apiculteur $user): Response
    {
        if ($apiary->getBeekeeper()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }
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
        if ($apiary->getBeekeeper()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }
        $declaration->createdoc($user, $apiary);
        $relativePath = PathMaker::makeApiaryDeclarationPath($user, $this->userFolderRoot);
        $userPath = $this->userFolderRoot . $relativePath;
        $filename = "declaration_{$apiary->getIdentification()}.pdf";
        $declaration->save($userPath, $filename);
        return $this->render('apiary/editions/print_declaration.html.twig', [
            'apiary' => $apiary,
            'file' => $relativePath . $filename
        ]);
    }
}
