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
    public string $categoria;

    #[Assert\Optional]
    public ?string $descripcion = null;

    #[Assert\NotBlank]
    public string $unidad;

    #[Assert\Optional]
    #[Assert\GreaterThanOrEqual(0)]
    public ?float $rangoDosisMin = null;

    #[Assert\Optional]
    #[Assert\GreaterThanOrEqual(0)]
    public ?float $rangoDosisMax = null;

    #[Assert\Optional]
    public ?string $notas = null;
}

class ProductUpdateDto
{
    #[Assert\Optional]
    public ?string $nombre = null;

    #[Assert\Optional]
    public ?string $categoria = null;

    #[Assert\Optional]
    public ?string $descripcion = null;

    #[Assert\Optional]
    public ?string $unidad = null;

    #[Assert\Optional]
    #[Assert\GreaterThanOrEqual(0)]
    public ?float $rangoDosisMin = null;

    #[Assert\Optional]
    #[Assert\GreaterThanOrEqual(0)]
    public ?float $rangoDosisMax = null;

    #[Assert\Optional]
    public ?string $notas = null;

    #[Assert\Optional]
    public ?bool $activo = null;
}
