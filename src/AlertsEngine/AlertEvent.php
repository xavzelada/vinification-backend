<?php

namespace App\AlertsEngine;

class AlertEvent
{
    public function __construct(
        public string $type,
        public string $alertId,
        public string $message,
        public array $payload = []
    ) {
    }
}
