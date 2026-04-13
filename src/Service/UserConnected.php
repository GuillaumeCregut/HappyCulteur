<?php

namespace App\Service;

use Symfony\Component\Security\Core\User\UserInterface;

class UserConnected
{
    public function __construct(private UserInterface $user)
    {
        
    }
   
    public function __invoke(): array
    {
        $this->cleanUp();
         //Supprimer les données utilisateurs temporaires dans le répertoire
        return [];
    }

    private function cleanUp(): void
    {
        //Remove users datas in his directory
    }
}