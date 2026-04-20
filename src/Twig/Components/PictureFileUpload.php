<?php

namespace App\Twig\Components;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class PictureFileUpload
{
    public FormView $formField;
    public string $formLabel;
    public ?string $previewPath;
}
