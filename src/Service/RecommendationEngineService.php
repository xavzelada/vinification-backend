<?php

namespace App\Service;

use App\Entity\Batch;
use App\Entity\Recommendation;
use App\Entity\RecommendationRule;
use App\Entity\Measurement;
use Doctrine\ORM\EntityManagerInterface;

class RecommendationEngineService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RuleEvaluator $evaluator
    ) {
    }

    /**
     * @return Recommendation[]
     */
    public function recalcForBatch(Batch $batch): array
    {
        $rules = $this->em->getRepository(RecommendationRule::class)->findBy([
            'bodega' => $batch->getBodega(),
            'etapa' => $batch->getEtapa(),
            'activa' => true
        ]);

        $inputs = $this->buildInputs($batch);
        $recommendations = [];

        foreach ($rules as $rule) {
            $conditions = $rule->getCondiciones();
            if (!is_array($conditions) || count($conditions) === 0) {
                continue;
            }
            $matched = 0;
            foreach ($conditions as $cond) {
                $field = $cond['field'] ?? null;
                $operator = $cond['operator'] ?? null;
                $value = $cond['value'] ?? null;
                $valueMax = $cond['valueMax'] ?? null;
                if (!$field || !$operator) {
                    continue;
                }
                $current = $inputs[$field] ?? null;
                if ($current === null) {
                    continue;
                }
                $ok = $this->evaluator->compare((float) $current, $operator, $value !== null ? (float) $value : null, $valueMax !== null ? (float) $valueMax : null);
                if ($ok) {
                    $matched++;
                }
            }

            if ($matched === 0) {
                continue;
            }

            $confidence = round(($matched / max(count($conditions), 1)) * 100, 2);

            $rec = new Recommendation();
            $rec->setLote($batch)
                ->setEtapa($batch->getEtapa())
                ->setEntradas($inputs)
                ->setAccionSugerida($rule->getAccionSugerida())
                ->setProducto($rule->getProducto())
                ->setDosisSugerida($rule->getDosisSugerida())
                ->setUnidad($rule->getUnidad())
                ->setExplicacion($rule->getExplicacion() ?? 'Regla heuristica. Sugerencia no vinculante.')
                ->setConfidence((string) $confidence)
                ->setEstado('sugerida');
            $this->em->persist($rec);
            $recommendations[] = $rec;
        }

        if (count($recommendations) > 0) {
            $this->em->flush();
        }

        return $recommendations;
    }

    private function buildInputs(Batch $batch): array
    {
        $latest = $this->em->getRepository(Measurement::class)->findOneBy([
            'lote' => $batch
        ], ['fechaHora' => 'DESC']);

        return [
            'densidad' => $latest ? (float) $latest->getDensidad() : null,
            'temperatura' => $latest ? (float) $latest->getTemperaturaC() : null,
            'brix' => $latest && $latest->getBrix() !== null ? (float) $latest->getBrix() : null
        ];
    }
}
