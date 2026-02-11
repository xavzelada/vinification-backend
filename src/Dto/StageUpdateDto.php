<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class StageUpdateDto
{
    #[Assert\Optional]
    public ?string $nombre = null;

    #[Assert\Optional]
    public ?int $orden = null;

    #[Assert\Optional]
    public ?string $descripcion = null;
}
