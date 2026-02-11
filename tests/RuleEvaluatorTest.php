<?php

namespace App\Tests;

use App\Enum\RuleOperator;
use App\Service\RuleEvaluator;
use PHPUnit\Framework\TestCase;

class RuleEvaluatorTest extends TestCase
{
    public function testCompare(): void
    {
        $eval = new RuleEvaluator();
        $this->assertTrue($eval->compare(10, RuleOperator::GT, 5));
        $this->assertTrue($eval->compare(10, RuleOperator::GTE, 10));
        $this->assertTrue($eval->compare(10, RuleOperator::BETWEEN, 5, 12));
        $this->assertFalse($eval->compare(10, RuleOperator::LT, 5));
    }

    public function testDeltaPerDay(): void
    {
        $eval = new RuleEvaluator();
        $this->assertTrue($eval->deltaPerDay(1.0, 2.0, 1, 0.5, 'gt'));
        $this->assertTrue($eval->deltaPerDay(2.0, 1.0, 1, 0.5, 'lt'));
    }

    public function testTrendWindow(): void
    {
        $eval = new RuleEvaluator();
        $this->assertTrue($eval->trendWindow(1.0, 3.0, 2, 0.5));
        $this->assertFalse($eval->trendWindow(1.0, 1.2, 2, 0.5));
    }
}
