<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class MeasurementUpdateDto
{
    #[Assert\Positive]
    public ?float $densidad = null;

    #[Assert\Positive]
    public ?float $temperaturaC = null;

    public ?float $brix = null;

    public ?string $comentario = null;

    #[Assert\DateTime]
    public ?string $fechaHora = null;
}