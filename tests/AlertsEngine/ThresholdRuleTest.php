<?php

namespace App\Tests\AlertsEngine;

use App\AlertsEngine\AlertEngine;
use App\AlertsEngine\AlertRule;
use PHPUnit\Framework\TestCase;

class ThresholdRuleTest extends TestCase
{
    public function testThresholdGreaterThan(): void
    {
        $engine = new AlertEngine();
        $rule = new AlertRule('r1', 'b1', 'e1', 'Densidad alta', AlertEngine::TYPE_THRESHOLD, 'densidad', AlertEngine::SEVERITY_WARN, true, 1.05, '>', null, null, null, null, null);

        $series = [
            ['timestamp' => 100, 'values' => ['densidad' => 1.02]],
            ['timestamp' => 200, 'values' => ['densidad' => 1.06]]
        ];

        $result = $engine->evaluate([$rule], $series, [], 'batch1', 'b1', 'e1');
        $this->assertCount(1, $result->newAlerts);
    }

    public function testThresholdLessThan(): void
    {
        $engine = new AlertEngine();
        $rule = new AlertRule('r2', 'b1', 'e1', 'Densidad baja', AlertEngine::TYPE_THRESHOLD, 'densidad', AlertEngine::SEVERITY_WARN, true, 1.00, '<', null, null, null, null, null);

        $series = [
            ['timestamp' => 100, 'values' => ['densidad' => 0.98]]
        ];

        $result = $engine->evaluate([$rule], $series, [], 'batch1', 'b1', 'e1');
        $this->assertCount(1, $result->newAlerts);
    }
}
