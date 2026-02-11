<?php

namespace App\AlertsEngine;

class AlertRule
{
    public function __construct(
        public string $id,
        public string $bodegaId,
        public string $etapaId,
        public string $name,
        public string $type,
        public string $field,
        public string $severity,
        public bool $active = true,
        public ?float $threshold = null,
        public ?string $thresholdOperator = null,
        public ?float $min = null,
        public ?float $max = null,
        public ?int $windowHours = null,
        public ?int $windowPoints = null,
        public ?float $slopeThreshold = null
    ) {
    }
}
