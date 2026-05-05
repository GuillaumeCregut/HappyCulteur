<?php

namespace App\Service;

use App\Entity\Apiculteur;

class UserConnected
{
    public function __construct()
    {
        
    }
   
    public function createEnv(Apiculteur $user, string $uploadPath): void
    {
        //TODO: Create Env
    }

    public function cleanUp(Apiculteur $user, string $uploadPath): void
    {
        //TODO : Remove users datas in his directory
        /*
            clean :
                - Documents
                - Dataloggers datas
                - Stats 

        */
        
    }
}