<?php

namespace App\Tests\RecommendationsEngine;

use App\RecommendationsEngine\RecommendationEngine;
use App\RecommendationsEngine\RecommendationInput;
use App\RecommendationsEngine\RecommendationRule;
use PHPUnit\Framework\TestCase;

class RecommendationEngineScoringTest extends TestCase
{
    public function testScoreIncreasesWithMatches(): void
    {
        $engine = new RecommendationEngine();
        $input = new RecommendationInput(
            'fermentacion',
            1000,
            ['densidad' => 1.05, 'temperatura' => 30],
            ['YAN' => 80],
            [],
            []
        );

        $rule = new RecommendationRule(
            'r1',
            'fermentacion',
            'control_temperatura',
            [
                ['source' => 'measurements', 'field' => 'temperatura', 'operator' => '>', 'value' => 28],
                ['source' => 'analyses', 'field' => 'YAN', 'operator' => '<', 'value' => 140]
            ],
            [],
            ['min' => 0, 'max' => 0, 'unit' => ''],
            ['Paso 1'],
            ['Precaucion'],
            0.5
        );

        $items = $engine->recommend($input, [$rule]);
        $this->assertCount(1, $items);
        $this->assertGreaterThan(0.5, $items[0]->score);
        $this->assertLessThanOrEqual(1.0, $items[0]->score);
    }

    public function testContraindicationsIncluded(): void
    {
        $engine = new RecommendationEngine();
        $input = new RecommendationInput('fermentacion', 1000, ['temperatura' => 30], [], [], []);
        $rule = new RecommendationRule(
            'r2',
            'fermentacion',
            'control_temperatura',
            [
                ['source' => 'measurements', 'field' => 'temperatura', 'operator' => '>', 'value' => 28]
            ],
            [],
            ['min' => 0, 'max' => 0, 'unit' => ''],
            ['Paso 1'],
            ['No aplicar choque termico'],
            0.6
        );

        $items = $engine->recommend($input, [$rule]);
        $this->assertSame('No aplicar choque termico', $items[0]->contraindications[0]);
    }
}
