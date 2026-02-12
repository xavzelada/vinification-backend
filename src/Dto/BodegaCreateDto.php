<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class BodegaCreateDto
{
    #[Assert\NotBlank]
    public string $codigo;

    #[Assert\NotBlank]
    public string $nombre;

    public ?string $pais = null;
}