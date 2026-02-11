<?php

namespace App\RecommendationsEngine;

class RecommendationItem
{
    public function __construct(
        public string $actionType,
        public float $score,
        public string $rationale,
        public array $suggestedProducts,
        public array $dosageRange,
        public array $steps,
        public array $contraindications,
        public string $disclaimer
    ) {
    }
}
