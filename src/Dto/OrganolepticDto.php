<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class OrganolepticCreateDto
{
    #[Assert\NotBlank]
    public int $loteId;

    #[Assert\NotBlank]
    #[Assert\Date]
    public string $fecha;

    #[Assert\Optional]
    public ?array $nariz = null;

    #[Assert\Optional]
    public ?array $boca = null;

    #[Assert\Optional]
    public ?array $color = null;

    #[Assert\Optional]
    public ?array $defectos = null;

    #[Assert\Optional]
    public ?string $intensidad = null;

    #[Assert\Optional]
    public ?string $notasLibres = null;
}

class OrganolepticUpdateDto
{
    #[Assert\Optional]
    public ?array $nariz = null;

    #[Assert\Optional]
    public ?array $boca = null;

    #[Assert\Optional]
    public ?array $color = null;

    #[Assert\Optional]
    public ?array $defectos = null;

    #[Assert\Optional]
    public ?string $intensidad = null;

    #[Assert\Optional]
    public ?string $notasLibres = null;
}
