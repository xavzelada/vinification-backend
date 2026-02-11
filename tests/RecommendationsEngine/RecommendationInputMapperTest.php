<?php

namespace App\Tests\RecommendationsEngine;

use App\Entity\Analysis;
use App\Entity\AnalysisType;
use App\Entity\Batch;
use App\Entity\Bodega;
use App\Entity\Measurement;
use App\Entity\Stage;
use App\RecommendationsEngine\RecommendationInputMapper;
use PHPUnit\Framework\TestCase;

class RecommendationInputMapperTest extends TestCase
{
    public function testMapFromBatch(): void
    {
        $bodega = new Bodega();
        $bodega->setCodigo('B1')->setNombre('B1');

        $stage = new Stage();
        $stage->setNombre('fermentacion')->setOrden(1)->setBodega($bodega);

        $batch = new Batch();
        $batch->setCodigo('L1')
            ->setVolumenLitros('1000')
            ->setVariedad('Tempranillo')
            ->setCosechaYear(2025)
            ->setBodega($bodega)
            ->setEtapa($stage);

        $m1 = new Measurement();
        $m1->setLote($batch)
            ->setUsuario(new \App\Entity\User())
            ->setFechaHora(new \DateTimeImmutable('2025-01-01 10:00:00'))
            ->setDensidad('1.02')
            ->setTemperaturaC('18.5');

        $m2 = new Measurement();
        $m2->setLote($batch)
            ->setUsuario(new \App\Entity\User())
            ->setFechaHora(new \DateTimeImmutable('2025-01-01 12:00:00'))
            ->setDensidad('1.03')
            ->setTemperaturaC('19.5');

        $type = new AnalysisType();
        $type->setCodigo('YAN')->setNombre('YAN')->setUnidad('mg/L');
        $analysis = new Analysis();
        $analysis->setLote($batch)
            ->setTipo($type)
            ->setUnidad('mg/L')
            ->setValor('120')
            ->setFechaMuestra(new \DateTimeImmutable('2025-01-01'));

        $mapper = new RecommendationInputMapper();
        $input = $mapper->fromBatch($batch, [$m1, $m2], [$analysis], [], []);

        $this->assertSame('fermentacion', $input->stage);
        $this->assertSame(1000.0, $input->batchVolumeLiters);
        $this->assertSame(1.03, $input->measurements['densidad']);
        $this->assertSame(19.5, $input->measurements['temperatura']);
        $this->assertSame(120.0, $input->analyses['YAN']);
    }
}
