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

    public ?float $brix = null;

    public ?string $comentario = null;

    #[Assert\DateTime]
    public ?string $fechaHora = null;
}