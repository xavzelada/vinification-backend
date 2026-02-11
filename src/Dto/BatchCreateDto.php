<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class BatchCreateDto
{
    #[Assert\NotBlank]
    public string $codigo;

    #[Assert\Positive]
    public float $volumenLitros;

    #[Assert\NotBlank]
    public string $variedad;

    #[Assert\Positive]
    public int $cosechaYear;

    #[Assert\NotBlank]
    public int $bodegaId;

    #[Assert\NotBlank]
    public int $etapaId;

    #[Assert\Optional]
    public ?int $ubicacionId = null;

    #[Assert\Optional]
    #[Assert\Date]
    public ?string $fechaInicio = null;

    #[Assert\Optional]
    #[Assert\Date]
    public ?string $fechaEmbotellado = null;

    #[Assert\Optional]
    public ?array $regulacion = null;
}
