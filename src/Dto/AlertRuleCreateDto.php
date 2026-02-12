<?php

namespace App\Dto;

use App\Enum\AlertSeverity;
use App\Enum\RuleOperator;
use Symfony\Component\Validator\Constraints as Assert;

class AlertRuleCreateDto
{
    #[Assert\NotBlank]
    public int $bodegaId;

    #[Assert\NotBlank]
    public int $etapaId;

    #[Assert\NotBlank]
    public string $nombre;

    #[Assert\NotBlank]
    public string $parametro;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: [
        RuleOperator::GT,
        RuleOperator::GTE,
        RuleOperator::LT,
        RuleOperator::LTE,
        RuleOperator::BETWEEN,
        RuleOperator::DELTA_PER_DAY,
        RuleOperator::TREND_WINDOW
    ])]
    public string $operador;

    public ?float $valor = null;

    public ?float $valorMax = null;

    public ?int $periodoDias = null;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: [AlertSeverity::INFO, AlertSeverity::WARN, AlertSeverity::CRIT])]
    public string $severidad;

    public ?bool $activa = true;

    public ?string $descripcion = null;
}