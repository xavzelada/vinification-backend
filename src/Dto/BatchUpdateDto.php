<?php

namespace App\Dto;

use App\Enum\BatchStatus;
use Symfony\Component\Validator\Constraints as Assert;

class BatchUpdateDto
{
    public ?string $codigo = null;

    public ?float $volumenLitros = null;

    public ?string $variedad = null;

    public ?int $cosechaYear = null;

    #[Assert\Choice(choices: [BatchStatus::ACTIVE, BatchStatus::CLOSED])]
    public ?string $estado = null;

    public ?int $etapaId = null;

    public ?int $ubicacionId = null;

    #[Assert\Date]
    public ?string $fechaInicio = null;

    #[Assert\Date]
    public ?string $fechaEmbotellado = null;

    public ?array $regulacion = null;
}