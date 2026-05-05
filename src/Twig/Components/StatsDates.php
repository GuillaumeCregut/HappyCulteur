<?php

namespace App\Twig\Components;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class StatsDates
{
    public FormView $startDate;
    public FormView $endDate;
    public string $typePage;
}
