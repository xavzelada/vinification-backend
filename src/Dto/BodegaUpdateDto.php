<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class BodegaUpdateDto
{
    public ?string $nombre = null;

    public ?string $pais = null;
}