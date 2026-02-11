<?php

namespace App\Tests\AlertsEngine;

use App\AlertsEngine\AlertEngine;
use App\AlertsEngine\AlertRule;
use PHPUnit\Framework\TestCase;

class RangeRuleTest extends TestCase
{
    public function testRangeBetween(): void
    {
        $engine = new AlertEngine();
        $rule = new AlertRule('r3', 'b1', 'e1', 'pH en rango', AlertEngine::TYPE_RANGE, 'pH', AlertEngine::SEVERITY_INFO, true, null, null, 3.2, 3.6, null, null, null);

        $series = [
            ['timestamp' => 100, 'values' => ['pH' => 3.4]]
        ];

        $result = $engine->evaluate([$rule], $series, [], 'batch1', 'b1', 'e1');
        $this->assertCount(1, $result->newAlerts);
    }
}
