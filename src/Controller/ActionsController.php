<?php

namespace App\Controller;

use App\Dto\ActionCreateDto;
use App\Dto\ActionUpdateDto;
use App\Entity\ActionApplied;
use App\Entity\Batch;
use App\Entity\Product;
use App\Entity\Stage;
use App\Enum\ActionStatus;
use App\Service\AuditService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/acciones')]
class ActionsController extends ApiController
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
            $items = $this->em->getRepository(ActionApplied::class)->findAll();
        } else {
            $qb = $this->em->createQueryBuilder();
            $items = $qb->select('a')
                ->from(ActionApplied::class, 'a')
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
        $item = $this->em->getRepository(ActionApplied::class)->find($id);
        if (!$item) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($item->getLote()->getBodega()->getId());
        return $this->jsonOk($item);
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_OPERADOR')]
    public function create(Request $request): Response
    {
        $data = $this->getJson($request);
        $dto = new ActionCreateDto();
        $dto->loteId = (int) ($data['loteId'] ?? 0);
        $dto->productoId = (int) ($data['productoId'] ?? 0);
        $dto->fecha = (string) ($data['fecha'] ?? '');
        $dto->dosis = (float) ($data['dosis'] ?? 0);
        $dto->unidad = (string) ($data['unidad'] ?? '');
        $dto->etapaId = (int) ($data['etapaId'] ?? 0);
        $dto->objetivo = $data['objetivo'] ?? null;
        $dto->observaciones = $data['observaciones'] ?? null;
        $dto->estado = $data['estado'] ?? null;
        $this->validateDto($dto);

        $lote = $this->em->getRepository(Batch::class)->find($dto->loteId);
        $producto = $this->em->getRepository(Product::class)->find($dto->productoId);
        $etapa = $this->em->getRepository(Stage::class)->find($dto->etapaId);
        if (!$lote || !$producto || !$etapa) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Lote, producto o etapa no encontrada');
        }
        $this->assertTenantBodegaId($lote->getBodega()->getId());
        $this->assertTenantBodegaId($producto->getBodega()->getId());
        $this->assertTenantBodegaId($etapa->getBodega()->getId());

        $user = $this->getUser();
        $action = new ActionApplied();
        $action->setLote($lote)
            ->setProducto($producto)
            ->setOperador($user)
            ->setFecha(new \DateTimeImmutable($dto->fecha))
            ->setDosis((string) $dto->dosis)
            ->setUnidad($dto->unidad)
            ->setEtapa($etapa)
            ->setObjetivo($dto->objetivo)
            ->setObservaciones($dto->observaciones)
            ->setEstado($dto->estado ?? ActionStatus::PENDING);

        $this->em->persist($action);
        $this->em->flush();

        $this->audit->log('acciones', (string) $action->getId(), 'create', null, ['loteId' => $lote->getId()], $user?->getId());

        return $this->jsonOk($action, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_OPERADOR')]
    public function update(int $id, Request $request): Response
    {
        $action = $this->em->getRepository(ActionApplied::class)->find($id);
        if (!$action) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($action->getLote()->getBodega()->getId());

        $data = $this->getJson($request);
        $dto = new ActionUpdateDto();
        $dto->dosis = $data['dosis'] ?? null;
        $dto->unidad = $data['unidad'] ?? null;
        $dto->objetivo = $data['objetivo'] ?? null;
        $dto->observaciones = $data['observaciones'] ?? null;
        $dto->estado = $data['estado'] ?? null;
        $this->validateDto($dto);

        if ($dto->dosis !== null) {
            $action->setDosis((string) $dto->dosis);
        }
        if ($dto->unidad !== null) {
            $action->setUnidad($dto->unidad);
        }
        if ($dto->objetivo !== null) {
            $action->setObjetivo($dto->objetivo);
        }
        if ($dto->observaciones !== null) {
            $action->setObservaciones($dto->observaciones);
        }
        if ($dto->estado !== null) {
            $action->setEstado($dto->estado);
        }

        $this->em->flush();
        return $this->jsonOk($action);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $action = $this->em->getRepository(ActionApplied::class)->find($id);
        if (!$action) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($action->getLote()->getBodega()->getId());
        $this->em->remove($action);
        $this->em->flush();
        return $this->jsonOk(['deleted' => true]);
    }
}
