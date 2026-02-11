<?php

namespace App\AlertsEngine;

class AlertEvaluationResult
{
    /** @var AlertRecord[] */
    public array $newAlerts = [];

    /** @var AlertRecord[] */
    public array $updatedAlerts = [];

    /** @var AlertEvent[] */
    public array $events = [];
}
