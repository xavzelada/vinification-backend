<?php

namespace App\RecommendationsEngine;

use App\Entity\ActionApplied;
use App\Entity\Analysis;
use App\Entity\Batch;
use App\Entity\Measurement;
use App\Entity\Product;

class RecommendationInputMapper
{
    /**
     * @param Measurement[] $measurements
     * @param Analysis[] $analyses
     * @param ActionApplied[] $actions
     * @param Product[] $products
     */
    public function fromBatch(Batch $batch, array $measurements, array $analyses, array $actions, array $products): RecommendationInput
    {
        $latestMeasurement = $this->latestMeasurement($measurements);
        $measurementMap = $latestMeasurement ? [
            'densidad' => (float) $latestMeasurement->getDensidad(),
            'temperatura' => (float) $latestMeasurement->getTemperaturaC(),
            'brix' => $latestMeasurement->getBrix() !== null ? (float) $latestMeasurement->getBrix() : null
        ] : [];

        $analysisMap = [];
        foreach ($analyses as $analysis) {
            if (!$analysis instanceof Analysis) {
                continue;
            }
            $code = $analysis->getTipo()->getCodigo();
            $analysisMap[$code] = (float) $analysis->getValor();
        }

        $recentActions = [];
        foreach ($actions as $action) {
            if (!$action instanceof ActionApplied) {
                continue;
            }
            $recentActions[] = [
                'id' => $action->getId(),
                'producto' => $action->getProducto()->getNombre(),
                'dosis' => $action->getDosis(),
                'unidad' => $action->getUnidad(),
                'fecha' => $action->getFecha()->format('Y-m-d')
            ];
        }

        $catalog = [];
        foreach ($products as $product) {
            if (!$product instanceof Product) {
                continue;
            }
            $catalog[] = [
                'id' => (string) $product->getId(),
                'name' => $product->getNombre(),
                'categoria' => $product->getCategoria(),
                'unidad' => $product->getUnidad(),
                'rangoMin' => $product->getRangoDosisMin(),
                'rangoMax' => $product->getRangoDosisMax()
            ];
        }

        return new RecommendationInput(
            $batch->getEtapa()->getNombre(),
            (float) $batch->getVolumenLitros(),
            array_filter($measurementMap, fn ($v) => $v !== null),
            $analysisMap,
            $recentActions,
            $catalog
        );
    }

    /** @param Measurement[] $measurements */
    private function latestMeasurement(array $measurements): ?Measurement
    {
        if (empty($measurements)) {
            return null;
        }
        usort($measurements, function (Measurement $a, Measurement $b) {
            return $a->getFechaHora() <=> $b->getFechaHora();
        });
        return $measurements[count($measurements) - 1];
    }
}
