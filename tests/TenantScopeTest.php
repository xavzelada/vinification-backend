<?php

namespace App\Tests;

use App\Entity\Batch;
use App\Enum\BatchStatus;
use App\Enum\UserRole;
use Symfony\Component\HttpFoundation\Response;

class TenantScopeTest extends ApiTestCase
{
    public function testCannotAccessOtherTenantBatch(): void
    {
        $bodega1 = $this->createBodega('B1');
        $bodega2 = $this->createBodega('B2');

        $stage1 = $this->createStage($bodega1, 'Fermentacion', 1);
        $stage2 = $this->createStage($bodega2, 'Fermentacion', 1);

        $user = $this->createUser($bodega1, 'read@test.com', UserRole::LECTURA, 'secret123');

        $batchOther = new Batch();
        $batchOther->setCodigo('L-999')
            ->setVolumenLitros('500')
            ->setVariedad('Malbec')
            ->setCosechaYear(2025)
            ->setEstado(BatchStatus::ACTIVE)
            ->setBodega($bodega2)
            ->setEtapa($stage2);
        $this->em->persist($batchOther);
        $this->em->flush();

        $token = $this->login('read@test.com', 'secret123');

        $client = static::createClient();
        $client->request('GET', '/batches/' . $batchOther->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token
        ]);

        $this->assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }
}
