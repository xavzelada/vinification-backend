<?php

namespace App\AlertsEngine;

class AlertEngine
{
    public const SEVERITY_INFO = 'info';
    public const SEVERITY_WARN = 'warn';
    public const SEVERITY_CRIT = 'critical';

    public const TYPE_THRESHOLD = 'threshold';
    public const TYPE_RANGE = 'range';
    public const TYPE_RATE_OF_CHANGE = 'rate_of_change';
    public const TYPE_TREND = 'trend';

    public function evaluate(
        array $rules,
        array $series,
        array $openAlerts,
        string $batchId,
        string $bodegaId,
        string $etapaId
    ): AlertEvaluationResult {
        $result = new AlertEvaluationResult();
        $latest = $this->getLatestPoint($series);

        foreach ($rules as $rule) {
            if (!$rule instanceof AlertRule || !$rule->active) {
                continue;
            }
            if ($rule->bodegaId !== $bodegaId || $rule->etapaId !== $etapaId) {
                continue;
            }

            $triggered = false;
            $value = null;
            $trendValue = null;

            switch ($rule->type) {
                case self::TYPE_THRESHOLD:
                    $value = $this->getFieldValue($latest, $rule->field);
                    if ($value !== null && $rule->threshold !== null && $rule->thresholdOperator) {
                        if ($rule->thresholdOperator === '>') {
                            $triggered = $value > $rule->threshold;
                        } elseif ($rule->thresholdOperator === '<') {
                            $triggered = $value < $rule->threshold;
                        }
                    }
                    break;
                case self::TYPE_RANGE:
                    $value = $this->getFieldValue($latest, $rule->field);
                    if ($value !== null && $rule->min !== null && $rule->max !== null) {
                        $triggered = $value >= $rule->min && $value <= $rule->max;
                    }
                    break;
                case self::TYPE_RATE_OF_CHANGE:
                    $roc = $this->rateOfChange($series, $rule->field, $rule->windowHours ?? 24);
                    if ($roc !== null && $rule->threshold !== null) {
                        $triggered = $roc > $rule->threshold;
                        $value = $roc;
                    }
                    break;
                case self::TYPE_TREND:
                    $trend = $this->trendSlope($series, $rule->field, $rule->windowPoints ?? 10);
                    if ($trend !== null && $rule->slopeThreshold !== null) {
                        $triggered = $trend > $rule->slopeThreshold;
                        $trendValue = $trend;
                        $value = $this->getFieldValue($latest, $rule->field);
                    }
                    break;
            }

            if (!$triggered || $value === null) {
                continue;
            }

            $existing = $this->findOpenAlert($openAlerts, $batchId, $rule->id, $rule->field);
            if ($existing) {
                $existing->lastSeenAt = new \DateTimeImmutable();
                $existing->message = $this->buildMessage($rule, $value, $trendValue);
                $existing->value = $value;
                $existing->trendValue = $trendValue;
                $result->updatedAlerts[] = $existing;
                $result->events[] = new AlertEvent('alert.updated', $existing->id, $existing->message, [
                    'batchId' => $batchId,
                    'ruleId' => $rule->id
                ]);
            } else {
                $now = new \DateTimeImmutable();
                $alert = new AlertRecord(
                    $this->newId(),
                    $batchId,
                    $rule->id,
                    $rule->field,
                    $rule->severity,
                    'abierta',
                    $this->buildMessage($rule, $value, $trendValue),
                    $value,
                    $trendValue,
                    $now,
                    $now
                );
                $result->newAlerts[] = $alert;
                $result->events[] = new AlertEvent('alert.created', $alert->id, $alert->message, [
                    'batchId' => $batchId,
                    'ruleId' => $rule->id
                ]);
            }
        }

        return $result;
    }

    private function getLatestPoint(array $series): ?array
    {
        if (empty($series)) {
            return null;
        }
        usort($series, fn ($a, $b) => $a['timestamp'] <=> $b['timestamp']);
        return $series[count($series) - 1];
    }

    private function getFieldValue(?array $point, string $field): ?float
    {
        if (!$point || !isset($point['values'][$field])) {
            return null;
        }
        return (float) $point['values'][$field];
    }

    private function rateOfChange(array $series, string $field, int $hours): ?float
    {
        if (count($series) < 2) {
            return null;
        }
        usort($series, fn ($a, $b) => $a['timestamp'] <=> $b['timestamp']);
        $latest = $series[count($series) - 1];
        $latestVal = $this->getFieldValue($latest, $field);
        if ($latestVal === null) {
            return null;
        }
        $targetTime = $latest['timestamp'] - ($hours * 3600);
        $past = null;
        foreach ($series as $point) {
            if ($point['timestamp'] <= $targetTime) {
                $past = $point;
            }
        }
        if (!$past) {
            return null;
        }
        $pastVal = $this->getFieldValue($past, $field);
        if ($pastVal === null) {
            return null;
        }
        $delta = $latestVal - $pastVal;
        return $delta / max($hours, 1);
    }

    private function trendSlope(array $series, string $field, int $points): ?float
    {
        if (count($series) < 2) {
            return null;
        }
        usort($series, fn ($a, $b) => $a['timestamp'] <=> $b['timestamp']);
        $slice = array_slice($series, -$points);
        $xs = [];
        $ys = [];
        foreach ($slice as $idx => $point) {
            $val = $this->getFieldValue($point, $field);
            if ($val === null) {
                continue;
            }
            $xs[] = $idx + 1;
            $ys[] = $val;
        }
        $n = count($xs);
        if ($n < 2) {
            return null;
        }
        $sumX = array_sum($xs);
        $sumY = array_sum($ys);
        $sumXY = 0.0;
        $sumXX = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $sumXY += $xs[$i] * $ys[$i];
            $sumXX += $xs[$i] * $xs[$i];
        }
        $den = ($n * $sumXX - $sumX * $sumX);
        if ($den == 0.0) {
            return null;
        }
        $slope = ($n * $sumXY - $sumX * $sumY) / $den;
        return $slope;
    }

    private function findOpenAlert(array $openAlerts, string $batchId, string $ruleId, string $field): ?AlertRecord
    {
        foreach ($openAlerts as $alert) {
            if ($alert instanceof AlertRecord && $alert->batchId === $batchId && $alert->ruleId === $ruleId && $alert->field === $field && $alert->status === 'abierta') {
                return $alert;
            }
        }
        return null;
    }

    private function buildMessage(AlertRule $rule, float $value, ?float $trend): string
    {
        if ($rule->type === self::TYPE_TREND && $trend !== null) {
            return sprintf('%s: valor=%.4f, slope=%.4f', $rule->name, $value, $trend);
        }
        return sprintf('%s: valor=%.4f', $rule->name, $value);
    }

    private function newId(): string
    {
        return bin2hex(random_bytes(8));
    }
}
