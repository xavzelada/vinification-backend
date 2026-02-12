<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class OrganolepticCreateDto
{
    #[Assert\NotBlank]
    public int $loteId;

    #[Assert\NotBlank]
    #[Assert\DateTime]
    public string $fechaHora;

    #[Assert\Type('array')]
    public ?array $nariz = null;

    #[Assert\Type('array')]
    public ?array $boca = null;

    #[Assert\Type('array')]
    public ?array $color = null;

    #[Assert\Type('array')]
    public ?array $defectos = null;

    public ?string $comentario = null;
}