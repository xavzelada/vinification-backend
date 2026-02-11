<?php

namespace App\Tests\RecommendationsEngine;

use App\RecommendationsEngine\RuleValidator;
use PHPUnit\Framework\TestCase;

class RuleValidatorTest extends TestCase
{
    public function testValidRulePasses(): void
    {
        $validator = new RuleValidator();
        $rules = [
            [
                'id' => 'r1',
                'stage' => 'fermentacion',
                'action_type' => 'control_temperatura',
                'conditions' => [
                    ['source' => 'measurements', 'field' => 'temperatura', 'operator' => '>', 'value' => 28]
                ],
                'suggested_products' => [],
                'dosage_range' => ['min' => 0, 'max' => 0, 'unit' => ''],
                'steps' => ['Paso 1'],
                'contraindications' => ['Precaucion'],
                'base_score' => 0.6
            ]
        ];
        $errors = $validator->validateRules($rules);
        $this->assertCount(0, $errors);
    }

    public function testInvalidRuleFails(): void
    {
        $validator = new RuleValidator();
        $rules = [
            [
                'id' => 'r1',
                'stage' => 'fermentacion',
                'action_type' => 'control_temperatura',
                'conditions' => [
                    ['source' => 'bad', 'field' => 'temperatura', 'operator' => '??', 'value' => 28]
                ],
                'suggested_products' => [],
                'dosage_range' => ['min' => 0, 'max' => 0],
                'steps' => 'Paso',
                'contraindications' => 'Precaucion'
            ]
        ];
        $errors = $validator->validateRules($rules);
        $this->assertGreaterThan(0, count($errors));
    }
}
