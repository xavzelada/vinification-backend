<?php

namespace App\Dto;

use App\Enum\ActionStatus;
use Symfony\Component\Validator\Constraints as Assert;

class ActionCreateDto
{
    #[Assert\NotBlank]
    public int $loteId;

    #[Assert\NotBlank]
    public int $productoId;

    #[Assert\NotBlank]
    #[Assert\Date]
    public string $fecha;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public float $dosis;

    #[Assert\NotBlank]
    public string $unidad;

    #[Assert\NotBlank]
    public int $etapaId;

    #[Assert\Optional]
    public ?string $objetivo = null;

    #[Assert\Optional]
    public ?string $observaciones = null;

    #[Assert\Optional]
    #[Assert\Choice(choices: [ActionStatus::PENDING, ActionStatus::CONFIRMED])]
    public ?string $estado = null;
}
