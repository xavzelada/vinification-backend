<?php

namespace App\Service;

use App\Entity\Alert;
use App\Entity\AlertRule;
use App\Entity\Batch;
use App\Entity\Measurement;
use App\Enum\AlertStatus;
use App\Enum\RuleOperator;
use Doctrine\ORM\EntityManagerInterface;

class AlertEngineService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RuleEvaluator $evaluator
    ) {
    }

    /**
     * @return Alert[]
     */
    public function evaluateForMeasurement(Batch $batch, Measurement $measurement): array
    {
        $rules = $this->em->getRepository(AlertRule::class)->findBy([
            'bodega' => $batch->getBodega(),
            'etapa' => $batch->getEtapa(),
            'activa' => true
        ]);

        $alerts = [];
        foreach ($rules as $rule) {
            $value = $this->getMeasurementValue($measurement, $rule->getParametro());
            if ($value === null) {
                continue;
            }

            $triggered = false;
            $tendencia = null;

            if (in_array($rule->getOperador(), [RuleOperator::GT, RuleOperator::GTE, RuleOperator::LT, RuleOperator::LTE, RuleOperator::BETWEEN], true)) {
                $triggered = $this->evaluator->compare($value, $rule->getOperador(), $rule->getValor() !== null ? (float) $rule->getValor() : null, $rule->getValorMax() !== null ? (float) $rule->getValorMax() : null);
            } elseif ($rule->getOperador() === RuleOperator::DELTA_PER_DAY || $rule->getOperador() === RuleOperator::TREND_WINDOW) {
                $windowDays = $rule->getPeriodoDias() ?? 2;
                $series = $this->getMeasurementSeries($batch, $windowDays, $rule->getParametro());
                if (count($series) >= 2) {
                    $first = $series[0]['value'];
                    $last = $series[count($series) - 1]['value'];
                    $days = max($windowDays, 1);
                    if ($rule->getOperador() === RuleOperator::DELTA_PER_DAY) {
                        $threshold = (float) ($rule->getValor() ?? 0);
                        $direction = $threshold < 0 ? 'lt' : 'gt';
                        $triggered = $this->evaluator->deltaPerDay($first, $last, $days, abs($threshold), $direction);
                        $tendencia = ($last - $first) / $days;
                    } else {
                        $threshold = (float) ($rule->getValor() ?? 0);
                        $triggered = $this->evaluator->trendWindow($first, $last, $days, $threshold);
                        $tendencia = ($last - $first) / $days;
                    }
                }
            }

            if ($triggered) {
                $alert = new Alert();
                $alert->setLote($batch)
                    ->setRegla($rule)
                    ->setSeveridad($rule->getSeveridad())
                    ->setEstado(AlertStatus::OPEN)
                    ->setMensaje($this->buildMessage($rule, $value, $tendencia))
                    ->setValorDetectado((string) $value)
                    ->setTendencia($tendencia !== null ? (string) $tendencia : null)
                    ->setDetectedAt(new \DateTimeImmutable());
                $this->em->persist($alert);
                $alerts[] = $alert;
            }
        }

        if (count($alerts) > 0) {
            $this->em->flush();
        }

        return $alerts;
    }

    private function getMeasurementValue(Measurement $measurement, string $param): ?float
    {
        return match ($param) {
            'densidad' => (float) $measurement->getDensidad(),
            'temperatura' => (float) $measurement->getTemperaturaC(),
            'brix' => $measurement->getBrix() !== null ? (float) $measurement->getBrix() : null,
            default => null
        };
    }

    private function getMeasurementSeries(Batch $batch, int $days, string $param): array
    {
        $since = (new \DateTimeImmutable())->modify(sprintf('-%d days', $days));
        $qb = $this->em->createQueryBuilder();
        $qb->select('m')
            ->from(Measurement::class, 'm')
            ->where('m.lote = :lote')
            ->andWhere('m.fechaHora >= :since')
            ->setParameter('lote', $batch)
            ->setParameter('since', $since)
            ->orderBy('m.fechaHora', 'ASC');
        $rows = $qb->getQuery()->getResult();

        $series = [];
        foreach ($rows as $row) {
            /** @var Measurement $row */
            $val = $this->getMeasurementValue($row, $param);
            if ($val === null) {
                continue;
            }
            $series[] = [
                'date' => $row->getFechaHora(),
                'value' => (float) $val
            ];
        }
        return $series;
    }

    private function buildMessage(AlertRule $rule, float $value, ?float $trend): string
    {
        if ($trend !== null) {
            return sprintf('%s: valor %s, tendencia %.4f', $rule->getNombre(), $value, $trend);
        }
        return sprintf('%s: valor %s', $rule->getNombre(), $value);
    }
}
