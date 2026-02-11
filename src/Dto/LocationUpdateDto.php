<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class LocationUpdateDto
{
    #[Assert\Optional]
    public ?string $nombre = null;

    #[Assert\Optional]
    public ?string $tipo = null;

    #[Assert\Optional]
    public ?float $capacidadLitros = null;
}
