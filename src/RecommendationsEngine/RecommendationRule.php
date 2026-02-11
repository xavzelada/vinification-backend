<?php

namespace App\RecommendationsEngine;

class RecommendationRule
{
    public function __construct(
        public string $id,
        public string $stage,
        public string $actionType,
        public array $conditions,
        public array $suggestedProducts,
        public array $dosageRange,
        public array $steps,
        public array $contraindications,
        public float $baseScore = 0.5
    ) {
    }
}
