<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class AnalysisUpdateDto
{
    public ?string $unidad = null;

    #[Assert\GreaterThanOrEqual(0)]
    public ?float $valor = null;

    public ?string $metodo = null;

    public ?string $laboratorio = null;

    #[Assert\Date]
    public ?string $fechaMuestra = null;

    #[Assert\Date]
    public ?string $fechaResultado = null;
}