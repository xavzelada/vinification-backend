<?php

namespace App\Controller;

use App\Dto\AnalysisCreateDto;
use App\Dto\AnalysisUpdateDto;
use App\Entity\Analysis;
use App\Entity\AnalysisType;
use App\Entity\Batch;
use App\Service\AuditService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/analyses')]
class AnalysesController extends ApiController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        private EntityManagerInterface $em,
        private AuditService $audit
    ) {
        parent::__construct($serializer, $validator);
    }

    #[Route('', methods: ['GET'])]
    public function list(): Response
    {
        if ($this->isAdmin()) {
            $items = $this->em->getRepository(Analysis::class)->findAll();
        } else {
            $qb = $this->em->createQueryBuilder();
            $items = $qb->select('a')
                ->from(Analysis::class, 'a')
                ->join('a.lote', 'l')
                ->where('l.bodega = :bodega')
                ->setParameter('bodega', $this->getActor()->getBodega())
                ->getQuery()
                ->getResult();
        }
        return $this->jsonOk($items);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getOne(int $id): Response
    {
        $item = $this->em->getRepository(Analysis::class)->find($id);
        if (!$item) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($item->getLote()->getBodega()->getId());
        return $this->jsonOk($item);
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_ENOLOGO')]
    public function create(Request $request): Response
    {
        $data = $this->getJson($request);
        $dto = new AnalysisCreateDto();
        $dto->loteId = (int) ($data['loteId'] ?? 0);
        $dto->tipoId = (int) ($data['tipoId'] ?? 0);
        $dto->unidad = (string) ($data['unidad'] ?? '');
        $dto->valor = (float) ($data['valor'] ?? 0);
        $dto->metodo = $data['metodo'] ?? null;
        $dto->laboratorio = $data['laboratorio'] ?? null;
        $dto->fechaMuestra = (string) ($data['fechaMuestra'] ?? '');
        $dto->fechaResultado = $data['fechaResultado'] ?? null;
        $this->validateDto($dto);

        $lote = $this->em->getRepository(Batch::class)->find($dto->loteId);
        $tipo = $this->em->getRepository(AnalysisType::class)->find($dto->tipoId);
        if (!$lote || !$tipo) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Lote o tipo no encontrado');
        }
        $this->assertTenantBodegaId($lote->getBodega()->getId());

        $analysis = new Analysis();
        $analysis->setLote($lote)
            ->setTipo($tipo)
            ->setUnidad($dto->unidad)
            ->setValor((string) $dto->valor)
            ->setMetodo($dto->metodo)
            ->setLaboratorio($dto->laboratorio)
            ->setFechaMuestra(new \DateTimeImmutable($dto->fechaMuestra));
        if ($dto->fechaResultado) {
            $analysis->setFechaResultado(new \DateTimeImmutable($dto->fechaResultado));
        }

        $this->em->persist($analysis);
        $this->em->flush();

        $actor = $this->getUser();
        $this->audit->log('analisis', (string) $analysis->getId(), 'create', null, ['tipo' => $tipo->getCodigo()], $actor?->getId());

        return $this->jsonOk($analysis, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_ENOLOGO')]
    public function update(int $id, Request $request): Response
    {
        $analysis = $this->em->getRepository(Analysis::class)->find($id);
        if (!$analysis) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($analysis->getLote()->getBodega()->getId());

        $data = $this->getJson($request);
        $dto = new AnalysisUpdateDto();
        $dto->unidad = $data['unidad'] ?? null;
        $dto->valor = $data['valor'] ?? null;
        $dto->metodo = $data['metodo'] ?? null;
        $dto->laboratorio = $data['laboratorio'] ?? null;
        $dto->fechaMuestra = $data['fechaMuestra'] ?? null;
        $dto->fechaResultado = $data['fechaResultado'] ?? null;
        $this->validateDto($dto);

        if ($dto->unidad !== null) {
            $analysis->setUnidad($dto->unidad);
        }
        if ($dto->valor !== null) {
            $analysis->setValor((string) $dto->valor);
        }
        if ($dto->metodo !== null) {
            $analysis->setMetodo($dto->metodo);
        }
        if ($dto->laboratorio !== null) {
            $analysis->setLaboratorio($dto->laboratorio);
        }
        if ($dto->fechaMuestra !== null) {
            $analysis->setFechaMuestra(new \DateTimeImmutable($dto->fechaMuestra));
        }
        if ($dto->fechaResultado !== null) {
            $analysis->setFechaResultado(new \DateTimeImmutable($dto->fechaResultado));
        }

        $this->em->flush();
        return $this->jsonOk($analysis);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $analysis = $this->em->getRepository(Analysis::class)->find($id);
        if (!$analysis) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($analysis->getLote()->getBodega()->getId());
        $this->em->remove($analysis);
        $this->em->flush();
        return $this->jsonOk(['deleted' => true]);
    }
}
