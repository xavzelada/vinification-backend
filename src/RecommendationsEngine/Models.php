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

class RecommendationInput
{
    public function __construct(
        public string $stage,
        public float $batchVolumeLiters,
        public array $measurements,
        public array $analyses,
        public array $recentActions,
        public array $catalogProducts
    ) {
    }
}

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
