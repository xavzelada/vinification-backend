<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RecommendationRuleUpdateDto
{
    public ?string $nombre = null;

    #[Assert\Type('array')]
    public ?array $condiciones = null;

    public ?string $accionSugerida = null;

    public ?int $productoId = null;

    public ?float $dosisSugerida = null;

    public ?string $unidad = null;

    public ?string $explicacion = null;

    public ?bool $activa = null;
}