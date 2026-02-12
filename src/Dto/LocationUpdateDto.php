<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class LocationUpdateDto
{
    public ?string $nombre = null;

    public ?string $tipo = null;

    public ?float $capacidadLitros = null;
}