<?php

namespace App\Tests;

use App\Entity\Batch;
use App\Enum\BatchStatus;
use App\Enum\UserRole;
use Symfony\Component\HttpFoundation\Response;

class BatchesMeasurementsTest extends ApiTestCase
{
    public function testCreateMeasurementFlow(): void
    {
        $bodega = $this->createBodega();
        $stage = $this->createStage($bodega, 'Fermentacion', 1);
        $user = $this->createUser($bodega, 'op@test.com', UserRole::OPERADOR, 'secret123');

        $batch = new Batch();
        $batch->setCodigo('L-001')
            ->setVolumenLitros('1000')
            ->setVariedad('Tempranillo')
            ->setCosechaYear(2025)
            ->setEstado(BatchStatus::ACTIVE)
            ->setBodega($bodega)
            ->setEtapa($stage);
        $this->em->persist($batch);
        $this->em->flush();

        $token = $this->login('op@test.com', 'secret123');

        $client = static::createClient();
        $client->request('POST', '/batches/' . $batch->getId() . '/measurements', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Authorization' => 'Bearer ' . $token
        ], json_encode([
            'densidad' => 1.05,
            'temperaturaC' => 18.5,
            'brix' => 20.0
        ]));

        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('measurement', $data);
        $this->assertArrayHasKey('alerts', $data);
        $this->assertArrayHasKey('recommendations', $data);
    }
}
