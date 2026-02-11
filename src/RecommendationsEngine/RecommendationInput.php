<?php

namespace App\RecommendationsEngine;

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
