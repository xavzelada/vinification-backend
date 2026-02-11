<?php

namespace App\Dto;

use App\Enum\ActionStatus;
use Symfony\Component\Validator\Constraints as Assert;

class ActionUpdateDto
{
    #[Assert\Optional]
    #[Assert\Positive]
    public ?float $dosis = null;

    #[Assert\Optional]
    public ?string $unidad = null;

    #[Assert\Optional]
    public ?string $objetivo = null;

    #[Assert\Optional]
    public ?string $observaciones = null;

    #[Assert\Optional]
    #[Assert\Choice(choices: [ActionStatus::PENDING, ActionStatus::CONFIRMED])]
    public ?string $estado = null;
}
