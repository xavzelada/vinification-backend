<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RecommendationRuleCreateDto
{
    #[Assert\NotBlank]
    public int $bodegaId;

    #[Assert\NotBlank]
    public int $etapaId;

    #[Assert\NotBlank]
    public string $nombre;

    #[Assert\NotBlank]
    #[Assert\Type('array')]
    public array $condiciones;

    #[Assert\NotBlank]
    public string $accionSugerida;

    public ?int $productoId = null;

    public ?float $dosisSugerida = null;

    public ?string $unidad = null;

    public ?string $explicacion = null;

    public ?bool $activa = true;
}