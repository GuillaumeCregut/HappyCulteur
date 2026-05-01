<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class DataloggerConfigDto
{
    #[Assert\NotBlank(message:'Veuillez entrer un nombre')]
    #[Assert\Range(
        min: 1,
        max: 24,
        notInRangeMessage: 'La valeur doit être entre {{ min }} et {{ max }}'
    )]
    public ?int $frequency = null;
    public bool $intTemp = false;
    public bool $intHygro = false;
    public bool $extTemp = false;
    public bool $extHygro = false;
    public bool $weight = false;
    public string $signature ='';
    public int $version = 0;
}
