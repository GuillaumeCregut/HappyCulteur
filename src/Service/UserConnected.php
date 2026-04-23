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

    }

    public function cleanUp(Apiculteur $user, string $uploadPath): void
    {
        //Remove users datas in his directory
        
    }
}