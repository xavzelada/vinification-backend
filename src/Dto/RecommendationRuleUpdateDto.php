<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RecommendationRuleUpdateDto
{
    #[Assert\Optional]
    public ?string $nombre = null;

    #[Assert\Optional]
    #[Assert\Type('array')]
    public ?array $condiciones = null;

    #[Assert\Optional]
    public ?string $accionSugerida = null;

    #[Assert\Optional]
    public ?int $productoId = null;

    #[Assert\Optional]
    public ?float $dosisSugerida = null;

    #[Assert\Optional]
    public ?string $unidad = null;

    #[Assert\Optional]
    public ?string $explicacion = null;

    #[Assert\Optional]
    public ?bool $activa = null;
}
