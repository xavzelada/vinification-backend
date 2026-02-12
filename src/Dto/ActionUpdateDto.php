<?php

namespace App\Dto;

use App\Enum\ActionStatus;
use Symfony\Component\Validator\Constraints as Assert;

class ActionUpdateDto
{
    #[Assert\Positive]
    public ?float $dosis = null;

    #[Assert\Choice(choices: ['g/hL', 'mL/hL', 'mg/L', 'g/L'])]
    public ?string $unidad = null;

    public ?string $objetivo = null;

    public ?string $observaciones = null;

    #[Assert\Choice(choices: [ActionStatus::PENDING, ActionStatus::CONFIRMED])]
    public ?string $estado = null;
}