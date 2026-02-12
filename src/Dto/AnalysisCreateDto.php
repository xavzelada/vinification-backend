<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class AnalysisCreateDto
{
    #[Assert\NotBlank]
    public int $loteId;

    #[Assert\NotBlank]
    public int $tipoId;

    #[Assert\NotBlank]
    public string $unidad;

    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(0)]
    public float $valor;

    public ?string $metodo = null;

    public ?string $laboratorio = null;

    #[Assert\NotBlank]
    #[Assert\Date]
    public string $fechaMuestra;

    #[Assert\Date]
    public ?string $fechaResultado = null;
}