<?php

namespace App\Tests\AlertsEngine;

use App\AlertsEngine\AlertEngine;
use App\AlertsEngine\AlertRule;
use PHPUnit\Framework\TestCase;

class TrendRuleTest extends TestCase
{
    public function testTrendSlope(): void
    {
        $engine = new AlertEngine();
        $rule = new AlertRule('r5', 'b1', 'e1', 'Tendencia densidad', AlertEngine::TYPE_TREND, 'densidad', AlertEngine::SEVERITY_WARN, true, null, null, null, null, null, 5, 0.01);

        $series = [
            ['timestamp' => 1, 'values' => ['densidad' => 1.000]],
            ['timestamp' => 2, 'values' => ['densidad' => 1.010]],
            ['timestamp' => 3, 'values' => ['densidad' => 1.020]],
            ['timestamp' => 4, 'values' => ['densidad' => 1.030]],
            ['timestamp' => 5, 'values' => ['densidad' => 1.040]]
        ];

        $result = $engine->evaluate([$rule], $series, [], 'batch1', 'b1', 'e1');
        $this->assertCount(1, $result->newAlerts);
    }
}
