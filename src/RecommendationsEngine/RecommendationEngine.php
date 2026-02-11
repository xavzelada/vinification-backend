<?php

namespace App\RecommendationsEngine;

class RecommendationEngine
{
    private const DISCLAIMER = 'Verificar con enologo responsable y normativa local.';

    /**
     * @param RecommendationRule[] $rules
     */
    public function recommend(RecommendationInput $input, array $rules): array
    {
        $items = [];
        foreach ($rules as $rule) {
            if (!$rule instanceof RecommendationRule) {
                continue;
            }
            if ($rule->stage !== $input->stage) {
                continue;
            }
            [$ok, $matched, $details] = $this->evaluateConditions($input, $rule->conditions);
            if (!$ok) {
                continue;
            }
            $score = min(1.0, max(0.0, $rule->baseScore + ($matched * 0.1)));

            $items[] = new RecommendationItem(
                $rule->actionType,
                $score,
                $this->buildRationale($rule, $details),
                $this->filterProducts($rule->suggestedProducts, $input->catalogProducts),
                $rule->dosageRange,
                $rule->steps,
                $rule->contraindications,
                self::DISCLAIMER
            );
        }

        usort($items, fn (RecommendationItem $a, RecommendationItem $b) => $b->score <=> $a->score);
        return $items;
    }

    private function evaluateConditions(RecommendationInput $input, array $conditions): array
    {
        $matched = 0;
        $details = [];
        foreach ($conditions as $cond) {
            $source = $cond['source'] ?? null;
            $field = $cond['field'] ?? null;
            $operator = $cond['operator'] ?? null;
            $value = $cond['value'] ?? null;
            $valueMax = $cond['valueMax'] ?? null;
            if (!$source || !$field || !$operator) {
                continue;
            }
            $current = $this->getValue($input, $source, $field);
            if ($current === null) {
                continue;
            }
            $ok = $this->compare($current, $operator, $value, $valueMax);
            if ($ok) {
                $matched++;
                $details[] = sprintf('%s.%s %s %s', $source, $field, $operator, $valueMax ?? $value);
            }
        }
        return [$matched > 0, $matched, $details];
    }

    private function getValue(RecommendationInput $input, string $source, string $field): ?float
    {
        if ($source === 'measurements') {
            return $input->measurements[$field] ?? null;
        }
        if ($source === 'analyses') {
            return $input->analyses[$field] ?? null;
        }
        return null;
    }

    private function compare(float $current, string $operator, mixed $value, mixed $valueMax): bool
    {
        if ($operator === '>') {
            return $value !== null && $current > (float) $value;
        }
        if ($operator === '<') {
            return $value !== null && $current < (float) $value;
        }
        if ($operator === 'between') {
            return $value !== null && $valueMax !== null && $current >= (float) $value && $current <= (float) $valueMax;
        }
        return false;
    }

    private function buildRationale(RecommendationRule $rule, array $details): string
    {
        $why = implode('; ', $details);
        return sprintf('Regla %s disparada. Valores: %s.', $rule->id, $why);
    }

    private function filterProducts(array $suggested, array $catalog): array
    {
        if (empty($catalog)) {
            return $suggested;
        }
        $catalogIds = array_column($catalog, 'id');
        return array_values(array_filter($suggested, fn ($p) => in_array($p['id'], $catalogIds, true)));
    }
}
