<?php

namespace App\AlertsEngine;

class AlertRecord
{
    public function __construct(
        public string $id,
        public string $batchId,
        public string $ruleId,
        public string $field,
        public string $severity,
        public string $status,
        public string $message,
        public float $value,
        public ?float $trendValue,
        public \DateTimeImmutable $firstSeenAt,
        public \DateTimeImmutable $lastSeenAt
    ) {
    }
}
