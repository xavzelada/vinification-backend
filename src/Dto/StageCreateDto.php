<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class StageCreateDto
{
    #[Assert\NotBlank]
    public string $nombre;

    #[Assert\Positive]
    public int $orden;

    public ?string $descripcion = null;

    #[Assert\NotBlank]
    public int $bodegaId;
}