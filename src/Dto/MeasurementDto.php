<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class MeasurementCreateDto
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public float $densidad;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public float $temperaturaC;

    #[Assert\Optional]
    public ?float $brix = null;

    #[Assert\Optional]
    public ?string $comentario = null;

    #[Assert\Optional]
    #[Assert\DateTime]
    public ?string $fechaHora = null;
}

class MeasurementUpdateDto
{
    #[Assert\Optional]
    #[Assert\Positive]
    public ?float $densidad = null;

    #[Assert\Optional]
    #[Assert\Positive]
    public ?float $temperaturaC = null;

    #[Assert\Optional]
    public ?float $brix = null;

    #[Assert\Optional]
    public ?string $comentario = null;

    #[Assert\Optional]
    #[Assert\DateTime]
    public ?string $fechaHora = null;
}
