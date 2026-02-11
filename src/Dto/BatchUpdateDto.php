<?php

namespace App\Dto;

use App\Enum\BatchStatus;
use Symfony\Component\Validator\Constraints as Assert;

class BatchUpdateDto
{
    #[Assert\Optional]
    public ?string $codigo = null;

    #[Assert\Optional]
    public ?float $volumenLitros = null;

    #[Assert\Optional]
    public ?string $variedad = null;

    #[Assert\Optional]
    public ?int $cosechaYear = null;

    #[Assert\Optional]
    #[Assert\Choice(choices: [BatchStatus::ACTIVE, BatchStatus::CLOSED])]
    public ?string $estado = null;

    #[Assert\Optional]
    public ?int $etapaId = null;

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
