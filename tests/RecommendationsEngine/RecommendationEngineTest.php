<?php

namespace App\Tests\RecommendationsEngine;

use App\RecommendationsEngine\RecommendationEngine;
use App\RecommendationsEngine\RecommendationInput;
use App\RecommendationsEngine\RecommendationRule;
use PHPUnit\Framework\TestCase;

class RecommendationEngineTest extends TestCase
{
    public function testRecommendReturnsItemsWithDisclaimer(): void
    {
        $engine = new RecommendationEngine();
        $input = new RecommendationInput(
            'fermentacion',
            1000,
            ['densidad' => 1.05, 'temperatura' => 30],
            ['yan' => 80],
            [],
            [
                ['id' => 'prod1', 'name' => 'Nutriente levaduras']
            ]
        );

        $rules = [
            new RecommendationRule(
                'r1',
                'fermentacion',
                'ajustar_temperatura',
                [
                    ['source' => 'measurements', 'field' => 'temperatura', 'operator' => '>', 'value' => 28]
                ],
                [
                    ['id' => 'prod1', 'name' => 'Nutriente levaduras']
                ],
                ['min' => 10, 'max' => 20, 'unit' => 'g/hL'],
                ['Revisar sistema de enfriamiento', 'Registrar ajuste'],
                ['No aplicar si hay bloqueo de fermentacion'],
                0.6
            )
        ];

        $items = $engine->recommend($input, $rules);
        $this->assertCount(1, $items);
        $this->assertSame('ajustar_temperatura', $items[0]->actionType);
        $this->assertNotEmpty($items[0]->disclaimer);
    }

    public function testNoMatchReturnsEmpty(): void
    {
        $engine = new RecommendationEngine();
        $input = new RecommendationInput('crianza', 1000, ['temperatura' => 15], [], [], []);
        $rules = [
            new RecommendationRule(
                'r2',
                'crianza',
                'ajustar_temperatura',
                [
                    ['source' => 'measurements', 'field' => 'temperatura', 'operator' => '>', 'value' => 25]
                ],
                [],
                ['min' => 0, 'max' => 0, 'unit' => ''],
                ['Revisar'],
                [],
                0.5
            )
        ];

        $items = $engine->recommend($input, $rules);
        $this->assertCount(0, $items);
    }
}
