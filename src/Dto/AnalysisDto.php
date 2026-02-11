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

    #[Assert\Optional]
    public ?string $metodo = null;

    #[Assert\Optional]
    public ?string $laboratorio = null;

    #[Assert\NotBlank]
    #[Assert\Date]
    public string $fechaMuestra;

    #[Assert\Optional]
    #[Assert\Date]
    public ?string $fechaResultado = null;
}

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
