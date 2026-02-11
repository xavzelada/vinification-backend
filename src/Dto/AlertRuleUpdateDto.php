<?php

namespace App\Dto;

use App\Enum\AlertSeverity;
use App\Enum\RuleOperator;
use Symfony\Component\Validator\Constraints as Assert;

class AlertRuleUpdateDto
{
    #[Assert\Optional]
    public ?string $nombre = null;

    #[Assert\Optional]
    public ?string $parametro = null;

    #[Assert\Optional]
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

    #[Assert\Optional]
    public ?float $valor = null;

    #[Assert\Optional]
    public ?float $valorMax = null;

    #[Assert\Optional]
    public ?int $periodoDias = null;

    #[Assert\Optional]
    #[Assert\Choice(choices: [AlertSeverity::INFO, AlertSeverity::WARN, AlertSeverity::CRIT])]
    public ?string $severidad = null;

    #[Assert\Optional]
    public ?bool $activa = null;

    #[Assert\Optional]
    public ?string $descripcion = null;
}
