<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class StageUpdateDto
{
    public ?string $nombre = null;

    public ?int $orden = null;

    public ?string $descripcion = null;
}