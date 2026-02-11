<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class StageCreateDto
{
    #[Assert\NotBlank]
    public string $nombre;

    #[Assert\Positive]
    public int $orden;

    #[Assert\Optional]
    public ?string $descripcion = null;

    #[Assert\NotBlank]
    public int $bodegaId;
}

class StageUpdateDto
{
    #[Assert\Optional]
    public ?string $nombre = null;

    #[Assert\Optional]
    public ?int $orden = null;

    #[Assert\Optional]
    public ?string $descripcion = null;
}
