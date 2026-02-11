<?php

namespace App\Service;

use App\Enum\RuleOperator;

class RuleEvaluator
{
    public function compare(float $value, string $operator, ?float $threshold = null, ?float $max = null): bool
    {
        switch ($operator) {
            case RuleOperator::GT:
                return $threshold !== null && $value > $threshold;
            case RuleOperator::GTE:
                return $threshold !== null && $value >= $threshold;
            case RuleOperator::LT:
                return $threshold !== null && $value < $threshold;
            case RuleOperator::LTE:
                return $threshold !== null && $value <= $threshold;
            case RuleOperator::BETWEEN:
                return $threshold !== null && $max !== null && $value >= $threshold && $value <= $max;
            default:
                return false;
        }
    }

    public function deltaPerDay(float $first, float $last, float $days, float $threshold, string $direction = 'gt'): bool
    {
        if ($days <= 0) {
            return false;
        }
        $delta = ($last - $first) / $days;
        if ($direction === 'lt') {
            return $delta < $threshold;
        }
        return $delta > $threshold;
    }

    public function trendWindow(float $first, float $last, float $days, float $threshold): bool
    {
        if ($days <= 0) {
            return false;
        }
        $slope = ($last - $first) / $days;
        return abs($slope) >= $threshold;
    }
}
