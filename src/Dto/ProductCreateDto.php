<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ProductCreateDto
{
    #[Assert\NotBlank]
    public int $bodegaId;

    #[Assert\NotBlank]
    public string $nombre;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['levadura', 'nutriente', 'clarificante', 'estabilizante', 'tanino', 'enzima', 'so2', 'SO2'])]
    public string $categoria;

    public ?string $descripcion = null;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['g/hL', 'mL/hL', 'mg/L', 'g/L'])]
    public string $unidad;

    #[Assert\GreaterThanOrEqual(0)]
    public ?float $rangoDosisMin = null;

    #[Assert\GreaterThanOrEqual(0)]
    public ?float $rangoDosisMax = null;

    public ?string $notas = null;

    #[Assert\Type('array')]
    public ?array $compatibilidades = null;

    #[Assert\Type('array')]
    public ?array $incompatibilidades = null;

    #[Assert\Type('array')]
    public ?array $restriccionesEtapas = null;
}