<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class OrganolepticUpdateDto
{
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