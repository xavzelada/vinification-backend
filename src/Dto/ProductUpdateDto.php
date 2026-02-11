<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

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
