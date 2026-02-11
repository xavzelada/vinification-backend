<?php

namespace App\Controller;

use App\Dto\BatchCreateDto;
use App\Dto\BatchUpdateDto;
use App\Dto\MeasurementCreateDto;
use App\Entity\Bodega;
use App\Entity\Batch;
use App\Entity\Location;
use App\Entity\Measurement;
use App\Entity\Stage;
use App\Enum\BatchStatus;
use App\Service\AlertEngineService;
use App\Service\AuditService;
use App\Service\RecommendationEngineService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/batches')]
class BatchesController extends ApiController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        private EntityManagerInterface $em,
        private AuditService $audit,
        private AlertEngineService $alertEngine,
        private RecommendationEngineService $recommendationEngine
    ) {
        parent::__construct($serializer, $validator);
    }

    #[Route('', methods: ['GET'])]
    public function list(): Response
    {
        if ($this->isAdmin()) {
            $items = $this->em->getRepository(Batch::class)->findAll();
        } else {
            $items = $this->em->getRepository(Batch::class)->findBy([
                'bodega' => $this->getActor()->getBodega()
            ]);
        }
        return $this->jsonOk($items);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getOne(int $id): Response
    {
        $item = $this->em->getRepository(Batch::class)->find($id);
        if (!$item) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($item->getBodega()->getId());
        return $this->jsonOk($item);
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $data = $this->getJson($request);
        $dto = new BatchCreateDto();
        $dto->codigo = (string) ($data['codigo'] ?? '');
        $dto->volumenLitros = (float) ($data['volumenLitros'] ?? 0);
        $dto->variedad = (string) ($data['variedad'] ?? '');
        $dto->cosechaYear = (int) ($data['cosechaYear'] ?? 0);
        $dto->bodegaId = (int) ($data['bodegaId'] ?? 0);
        $dto->etapaId = (int) ($data['etapaId'] ?? 0);
        $dto->ubicacionId = $data['ubicacionId'] ?? null;
        $dto->fechaInicio = $data['fechaInicio'] ?? null;
        $dto->fechaEmbotellado = $data['fechaEmbotellado'] ?? null;
        $dto->regulacion = $data['regulacion'] ?? null;
        $this->validateDto($dto);

        $bodega = $this->em->getRepository(Bodega::class)->find($dto->bodegaId);
        $etapa = $this->em->getRepository(Stage::class)->find($dto->etapaId);
        if (!$bodega || !$etapa) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Bodega o etapa no encontrada');
        }
        $this->assertTenantBodegaId($bodega->getId());
        $ubicacion = null;
        if ($dto->ubicacionId) {
            $ubicacion = $this->em->getRepository(Location::class)->find((int) $dto->ubicacionId);
            if ($ubicacion) {
                $this->assertTenantBodegaId($ubicacion->getBodega()->getId());
            }
        }

        $batch = new Batch();
        $batch->setCodigo($dto->codigo)
            ->setVolumenLitros((string) $dto->volumenLitros)
            ->setVariedad($dto->variedad)
            ->setCosechaYear($dto->cosechaYear)
            ->setBodega($bodega)
            ->setEtapa($etapa)
            ->setUbicacion($ubicacion)
            ->setEstado(BatchStatus::ACTIVE)
            ->setRegulacion($dto->regulacion);

        if ($dto->fechaInicio) {
            $batch->setFechaInicio(new \DateTimeImmutable($dto->fechaInicio));
        }
        if ($dto->fechaEmbotellado) {
            $batch->setFechaEmbotellado(new \DateTimeImmutable($dto->fechaEmbotellado));
        }

        $this->em->persist($batch);
        $this->em->flush();

        $actor = $this->getUser();
        $this->audit->log('lotes', (string) $batch->getId(), 'create', null, ['codigo' => $batch->getCodigo()], $actor?->getId());

        return $this->jsonOk($batch, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, Request $request): Response
    {
        $batch = $this->em->getRepository(Batch::class)->find($id);
        if (!$batch) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($batch->getBodega()->getId());

        $data = $this->getJson($request);
        $dto = new BatchUpdateDto();
        $dto->codigo = $data['codigo'] ?? null;
        $dto->volumenLitros = $data['volumenLitros'] ?? null;
        $dto->variedad = $data['variedad'] ?? null;
        $dto->cosechaYear = $data['cosechaYear'] ?? null;
        $dto->estado = $data['estado'] ?? null;
        $dto->etapaId = $data['etapaId'] ?? null;
        $dto->ubicacionId = $data['ubicacionId'] ?? null;
        $dto->fechaInicio = $data['fechaInicio'] ?? null;
        $dto->fechaEmbotellado = $data['fechaEmbotellado'] ?? null;
        $dto->regulacion = $data['regulacion'] ?? null;
        $this->validateDto($dto);

        if ($dto->codigo !== null) {
            $batch->setCodigo($dto->codigo);
        }
        if ($dto->volumenLitros !== null) {
            $batch->setVolumenLitros((string) $dto->volumenLitros);
        }
        if ($dto->variedad !== null) {
            $batch->setVariedad($dto->variedad);
        }
        if ($dto->cosechaYear !== null) {
            $batch->setCosechaYear($dto->cosechaYear);
        }
        if ($dto->estado !== null) {
            $batch->setEstado($dto->estado);
        }
        if ($dto->etapaId !== null) {
            $etapa = $this->em->getRepository(Stage::class)->find((int) $dto->etapaId);
            if ($etapa) {
                $this->assertTenantBodegaId($etapa->getBodega()->getId());
                $batch->setEtapa($etapa);
            }
        }
        if ($dto->ubicacionId !== null) {
            $ubicacion = $this->em->getRepository(Location::class)->find((int) $dto->ubicacionId);
            if ($ubicacion) {
                $this->assertTenantBodegaId($ubicacion->getBodega()->getId());
            }
            $batch->setUbicacion($ubicacion);
        }
        if ($dto->fechaInicio) {
            $batch->setFechaInicio(new \DateTimeImmutable($dto->fechaInicio));
        }
        if ($dto->fechaEmbotellado) {
            $batch->setFechaEmbotellado(new \DateTimeImmutable($dto->fechaEmbotellado));
        }
        if ($dto->regulacion !== null) {
            $batch->setRegulacion($dto->regulacion);
        }

        $this->em->flush();

        $actor = $this->getUser();
        $this->audit->log('lotes', (string) $batch->getId(), 'update', null, ['codigo' => $batch->getCodigo()], $actor?->getId());

        return $this->jsonOk($batch);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $batch = $this->em->getRepository(Batch::class)->find($id);
        if (!$batch) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($batch->getBodega()->getId());
        $this->em->remove($batch);
        $this->em->flush();
        return $this->jsonOk(['deleted' => true]);
    }

    #[Route('/{id}/measurements', methods: ['POST'])]
    #[IsGranted('ROLE_OPERADOR')]
    public function createMeasurement(int $id, Request $request): Response
    {
        $batch = $this->em->getRepository(Batch::class)->find($id);
        if (!$batch) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Lote no encontrado');
        }
        $this->assertTenantBodegaId($batch->getBodega()->getId());

        $data = $this->getJson($request);
        $dto = new MeasurementCreateDto();
        $dto->densidad = (float) ($data['densidad'] ?? 0);
        $dto->temperaturaC = (float) ($data['temperaturaC'] ?? 0);
        $dto->brix = $data['brix'] ?? null;
        $dto->comentario = $data['comentario'] ?? null;
        $dto->fechaHora = $data['fechaHora'] ?? null;
        $this->validateDto($dto);

        $user = $this->getUser();
        $measurement = new Measurement();
        $measurement->setLote($batch)
            ->setUsuario($user)
            ->setDensidad((string) $dto->densidad)
            ->setTemperaturaC((string) $dto->temperaturaC)
            ->setBrix($dto->brix !== null ? (string) $dto->brix : null)
            ->setComentario($dto->comentario)
            ->setFechaHora($dto->fechaHora ? new \DateTimeImmutable($dto->fechaHora) : new \DateTimeImmutable());

        $this->em->persist($measurement);
        $this->em->flush();

        $alerts = $this->alertEngine->evaluateForMeasurement($batch, $measurement);
        $recs = $this->recommendationEngine->recalcForBatch($batch);

        $this->audit->log('mediciones', (string) $measurement->getId(), 'create', null, ['densidad' => $measurement->getDensidad()], $user?->getId());

        return $this->jsonOk([
            'measurement' => $measurement,
            'alerts' => $alerts,
            'recommendations' => $recs
        ], 201);
    }
}
