<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DoseCalculator
{
    public const UNIT_G_PER_HL = 'g/hL';
    public const UNIT_ML_PER_HL = 'mL/hL';
    public const UNIT_MG_PER_L = 'mg/L';
    public const UNIT_G_PER_L = 'g/L';

    private const UNIT_MAP = [
        'g/hl' => self::UNIT_G_PER_HL,
        'g/hL' => self::UNIT_G_PER_HL,
        'g/l' => self::UNIT_G_PER_L,
        'g/L' => self::UNIT_G_PER_L,
        'mg/l' => self::UNIT_MG_PER_L,
        'ml/hl' => self::UNIT_ML_PER_HL,
        'ml/hL' => self::UNIT_ML_PER_HL,
        'mL/hL' => self::UNIT_ML_PER_HL,
        'mg/L' => self::UNIT_MG_PER_L,
    ];

    public function normalizeUnit(string $unit): string
    {
        $key = str_replace(' ', '', $unit);
        $key = strtolower($key);
        if (!isset(self::UNIT_MAP[$key])) {
            throw new BadRequestHttpException('Unidad no soportada: ' . $unit);
        }
        return self::UNIT_MAP[$key];
    }

    public function convertDose(float $dose, string $fromUnit, string $toUnit): ?float
    {
        $from = $this->normalizeUnit($fromUnit);
        $to = $this->normalizeUnit($toUnit);

        if ($from === $to) {
            return $dose;
        }

        if ($this->isMassPerVolume($from) && $this->isMassPerVolume($to)) {
            if ($from === self::UNIT_G_PER_HL && $to === self::UNIT_MG_PER_L) {
                return $dose * 10.0;
            }
            if ($from === self::UNIT_MG_PER_L && $to === self::UNIT_G_PER_HL) {
                return $dose / 10.0;
            }
            if ($from === self::UNIT_G_PER_L && $to === self::UNIT_MG_PER_L) {
                return $dose * 1000.0;
            }
            if ($from === self::UNIT_MG_PER_L && $to === self::UNIT_G_PER_L) {
                return $dose / 1000.0;
            }
            if ($from === self::UNIT_G_PER_L && $to === self::UNIT_G_PER_HL) {
                return $dose * 100.0;
            }
            if ($from === self::UNIT_G_PER_HL && $to === self::UNIT_G_PER_L) {
                return $dose / 100.0;
            }
        }

        if ($this->isVolumePerVolume($from) && $this->isVolumePerVolume($to)) {
            return $dose;
        }

        return null;
    }

    public function perHlEquivalent(float $dose, string $unit): array
    {
        $normalized = $this->normalizeUnit($unit);
        if ($normalized === self::UNIT_MG_PER_L) {
            return [
                'value' => $dose * 100.0,
                'unit' => 'mg/hL'
            ];
        }
        if ($normalized === self::UNIT_G_PER_L) {
            return [
                'value' => $dose * 100.0,
                'unit' => self::UNIT_G_PER_HL
            ];
        }

        return [
            'value' => $dose,
            'unit' => $normalized
        ];
    }

    public function totalForVolume(float $dose, string $unit, float $volumeLiters): array
    {
        $normalized = $this->normalizeUnit($unit);
        if ($volumeLiters < 0) {
            throw new BadRequestHttpException('Volumen inválido');
        }

        if ($normalized === self::UNIT_G_PER_HL) {
            return [
                'value' => ($dose * $volumeLiters) / 100.0,
                'unit' => 'g'
            ];
        }
        if ($normalized === self::UNIT_G_PER_L) {
            return [
                'value' => $dose * $volumeLiters,
                'unit' => 'g'
            ];
        }

        if ($normalized === self::UNIT_ML_PER_HL) {
            return [
                'value' => ($dose * $volumeLiters) / 100.0,
                'unit' => 'mL'
            ];
        }

        return [
            'value' => $dose * $volumeLiters,
            'unit' => 'mg'
        ];
    }

    private function isMassPerVolume(string $unit): bool
    {
        return in_array($unit, [self::UNIT_G_PER_HL, self::UNIT_MG_PER_L, self::UNIT_G_PER_L], true);
    }

    private function isVolumePerVolume(string $unit): bool
    {
        return $unit === self::UNIT_ML_PER_HL;
    }
}
