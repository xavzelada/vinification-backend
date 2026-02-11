<?php

namespace App\Tests\AlertsEngine;

use App\AlertsEngine\AlertEngine;
use App\AlertsEngine\AlertRule;
use PHPUnit\Framework\TestCase;

class RateOfChangeRuleTest extends TestCase
{
    public function testRateOfChange(): void
    {
        $engine = new AlertEngine();
        $rule = new AlertRule('r4', 'b1', 'e1', 'Temp sube rapido', AlertEngine::TYPE_RATE_OF_CHANGE, 'temperatura', AlertEngine::SEVERITY_WARN, true, 0.5, null, null, null, 6, null, null);

        $series = [
            ['timestamp' => 0, 'values' => ['temperatura' => 10]],
            ['timestamp' => 6 * 3600, 'values' => ['temperatura' => 14]]
        ];

        $result = $engine->evaluate([$rule], $series, [], 'batch1', 'b1', 'e1');
        $this->assertCount(1, $result->newAlerts);
    }
}
