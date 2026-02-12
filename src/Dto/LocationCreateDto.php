<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class LocationCreateDto
{
    #[Assert\NotBlank]
    public string $nombre;

    #[Assert\NotBlank]
    public string $tipo;

    public ?float $capacidadLitros = null;

    #[Assert\NotBlank]
    public int $bodegaId;
}