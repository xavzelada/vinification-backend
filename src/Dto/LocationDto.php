<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class LocationCreateDto
{
    #[Assert\NotBlank]
    public string $nombre;

    #[Assert\NotBlank]
    public string $tipo;

    #[Assert\Optional]
    public ?float $capacidadLitros = null;

    #[Assert\NotBlank]
    public int $bodegaId;
}

class LocationUpdateDto
{
    #[Assert\Optional]
    public ?string $nombre = null;

    #[Assert\Optional]
    public ?string $tipo = null;

    #[Assert\Optional]
    public ?float $capacidadLitros = null;
}
