<?php

namespace App\Enum;

final class RuleOperator
{
    public const GT = '>';
    public const LT = '<';
    public const GTE = '>=';
    public const LTE = '<=';
    public const BETWEEN = 'between';
    public const DELTA_PER_DAY = 'delta_per_day';
    public const TREND_WINDOW = 'trend_window';
}
