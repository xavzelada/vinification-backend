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

    #[Assert\Optional]
    public ?int $productoId = null;

    #[Assert\Optional]
    public ?float $dosisSugerida = null;

    #[Assert\Optional]
    public ?string $unidad = null;

    #[Assert\Optional]
    public ?string $explicacion = null;

    #[Assert\Optional]
    public ?bool $activa = true;
}
