<?php

namespace App\RecommendationsEngine;

class RuleValidator
{
    private const ALLOWED_OPERATORS = ['>', '<', 'between'];

    public function validateRules(array $rules): array
    {
        $errors = [];
        foreach ($rules as $idx => $rule) {
            if (!is_array($rule)) {
                $errors[] = sprintf('Rule[%d]: must be object', $idx);
                continue;
            }
            $errors = array_merge($errors, $this->validateRule($rule, $idx));
        }
        return $errors;
    }

    private function validateRule(array $rule, int $idx): array
    {
        $errors = [];
        foreach (['id', 'stage', 'action_type', 'conditions', 'suggested_products', 'dosage_range', 'steps', 'contraindications'] as $field) {
            if (!array_key_exists($field, $rule)) {
                $errors[] = sprintf('Rule[%d].%s is required', $idx, $field);
            }
        }

        if (isset($rule['conditions']) && is_array($rule['conditions'])) {
            foreach ($rule['conditions'] as $cidx => $cond) {
                if (!is_array($cond)) {
                    $errors[] = sprintf('Rule[%d].conditions[%d] must be object', $idx, $cidx);
                    continue;
                }
                if (!in_array($cond['source'] ?? '', ['measurements', 'analyses'], true)) {
                    $errors[] = sprintf('Rule[%d].conditions[%d].source invalid', $idx, $cidx);
                }
                if (!in_array($cond['operator'] ?? '', self::ALLOWED_OPERATORS, true)) {
                    $errors[] = sprintf('Rule[%d].conditions[%d].operator invalid', $idx, $cidx);
                }
                if (!isset($cond['field'])) {
                    $errors[] = sprintf('Rule[%d].conditions[%d].field required', $idx, $cidx);
                }
            }
        }

        if (isset($rule['dosage_range']) && is_array($rule['dosage_range'])) {
            foreach (['min', 'max', 'unit'] as $f) {
                if (!array_key_exists($f, $rule['dosage_range'])) {
                    $errors[] = sprintf('Rule[%d].dosage_range.%s required', $idx, $f);
                }
            }
        }

        if (isset($rule['base_score'])) {
            $score = $rule['base_score'];
            if (!is_numeric($score) || $score < 0 || $score > 1) {
                $errors[] = sprintf('Rule[%d].base_score must be 0-1', $idx);
            }
        }

        if (isset($rule['steps']) && !is_array($rule['steps'])) {
            $errors[] = sprintf('Rule[%d].steps must be array', $idx);
        }

        if (isset($rule['contraindications']) && !is_array($rule['contraindications'])) {
            $errors[] = sprintf('Rule[%d].contraindications must be array', $idx);
        }

        return $errors;
    }
}
