<?php

namespace App\Controller;

use App\Dto\StageCreateDto;
use App\Dto\StageUpdateDto;
use App\Entity\Bodega;
use App\Entity\Stage;
use App\Service\AuditService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/etapas')]
class StagesController extends ApiController
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
            $items = $this->em->getRepository(Stage::class)->findBy([], ['orden' => 'ASC']);
        } else {
            $items = $this->em->getRepository(Stage::class)->findBy(
                ['bodega' => $this->getActor()->getBodega()],
                ['orden' => 'ASC']
            );
        }
        return $this->jsonOk($items);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getOne(int $id): Response
    {
        $item = $this->em->getRepository(Stage::class)->find($id);
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
        $dto = new StageCreateDto();
        $dto->nombre = (string) ($data['nombre'] ?? '');
        $dto->orden = (int) ($data['orden'] ?? 0);
        $dto->descripcion = $data['descripcion'] ?? null;
        $dto->bodegaId = (int) ($data['bodegaId'] ?? 0);
        $this->validateDto($dto);

        $bodega = $this->em->getRepository(Bodega::class)->find($dto->bodegaId);
        if (!$bodega) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Bodega no encontrada');
        }
        $this->assertTenantBodegaId($bodega->getId());

        $stage = new Stage();
        $stage->setNombre($dto->nombre)
            ->setOrden($dto->orden)
            ->setDescripcion($dto->descripcion)
            ->setBodega($bodega);

        $this->em->persist($stage);
        $this->em->flush();

        $actor = $this->getUser();
        $this->audit->log('etapas', (string) $stage->getId(), 'create', null, ['nombre' => $stage->getNombre()], $actor?->getId());

        return $this->jsonOk($stage, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, Request $request): Response
    {
        $stage = $this->em->getRepository(Stage::class)->find($id);
        if (!$stage) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($stage->getBodega()->getId());
        $before = ['nombre' => $stage->getNombre(), 'orden' => $stage->getOrden()];

        $data = $this->getJson($request);
        $dto = new StageUpdateDto();
        $dto->nombre = $data['nombre'] ?? null;
        $dto->orden = $data['orden'] ?? null;
        $dto->descripcion = $data['descripcion'] ?? null;
        $this->validateDto($dto);

        if ($dto->nombre !== null) {
            $stage->setNombre($dto->nombre);
        }
        if ($dto->orden !== null) {
            $stage->setOrden($dto->orden);
        }
        if ($dto->descripcion !== null) {
            $stage->setDescripcion($dto->descripcion);
        }

        $this->em->flush();

        $actor = $this->getUser();
        $this->audit->log('etapas', (string) $stage->getId(), 'update', $before, ['nombre' => $stage->getNombre(), 'orden' => $stage->getOrden()], $actor?->getId());

        return $this->jsonOk($stage);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $stage = $this->em->getRepository(Stage::class)->find($id);
        if (!$stage) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->em->remove($stage);
        $this->em->flush();
        return $this->jsonOk(['deleted' => true]);
    }
}
