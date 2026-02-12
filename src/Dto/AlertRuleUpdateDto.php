<?php

namespace App\Dto;

use App\Enum\AlertSeverity;
use App\Enum\RuleOperator;
use Symfony\Component\Validator\Constraints as Assert;

class AlertRuleUpdateDto
{
    public ?string $nombre = null;

    public ?string $parametro = null;

    #[Assert\Choice(choices: [
        RuleOperator::GT,
        RuleOperator::GTE,
        RuleOperator::LT,
        RuleOperator::LTE,
        RuleOperator::BETWEEN,
        RuleOperator::DELTA_PER_DAY,
        RuleOperator::TREND_WINDOW
    ])]
    public ?string $operador = null;

    public ?float $valor = null;

    public ?float $valorMax = null;

    public ?int $periodoDias = null;

    #[Assert\Choice(choices: [AlertSeverity::INFO, AlertSeverity::WARN, AlertSeverity::CRIT])]
    public ?string $severidad = null;

    public ?bool $activa = null;

    public ?string $descripcion = null;
}