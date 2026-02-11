<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class AnalysisUpdateDto
{
    #[Assert\Optional]
    public ?string $unidad = null;

    #[Assert\Optional]
    #[Assert\GreaterThanOrEqual(0)]
    public ?float $valor = null;

    #[Assert\Optional]
    public ?string $metodo = null;

    #[Assert\Optional]
    public ?string $laboratorio = null;

    #[Assert\Optional]
    #[Assert\Date]
    public ?string $fechaMuestra = null;

    #[Assert\Optional]
    #[Assert\Date]
    public ?string $fechaResultado = null;
}
