<?php

namespace App\Controller;

use App\Dto\LocationCreateDto;
use App\Dto\LocationUpdateDto;
use App\Entity\Bodega;
use App\Entity\Location;
use App\Service\AuditService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/ubicaciones')]
class LocationsController extends ApiController
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
            $items = $this->em->getRepository(Location::class)->findAll();
        } else {
            $items = $this->em->getRepository(Location::class)->findBy([
                'bodega' => $this->getActor()->getBodega()
            ]);
        }
        return $this->jsonOk($items);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getOne(int $id): Response
    {
        $item = $this->em->getRepository(Location::class)->find($id);
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
        $dto = new LocationCreateDto();
        $dto->nombre = (string) ($data['nombre'] ?? '');
        $dto->tipo = (string) ($data['tipo'] ?? '');
        $dto->capacidadLitros = $data['capacidadLitros'] ?? null;
        $dto->bodegaId = (int) ($data['bodegaId'] ?? 0);
        $this->validateDto($dto);

        $bodega = $this->em->getRepository(Bodega::class)->find($dto->bodegaId);
        if (!$bodega) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Bodega no encontrada');
        }
        $this->assertTenantBodegaId($bodega->getId());

        $loc = new Location();
        $loc->setNombre($dto->nombre)
            ->setTipo($dto->tipo)
            ->setCapacidadLitros($dto->capacidadLitros !== null ? (string) $dto->capacidadLitros : null)
            ->setBodega($bodega);

        $this->em->persist($loc);
        $this->em->flush();

        $actor = $this->getUser();
        $this->audit->log('ubicaciones', (string) $loc->getId(), 'create', null, ['nombre' => $loc->getNombre()], $actor?->getId());

        return $this->jsonOk($loc, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, Request $request): Response
    {
        $loc = $this->em->getRepository(Location::class)->find($id);
        if (!$loc) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($loc->getBodega()->getId());
        $before = ['nombre' => $loc->getNombre(), 'tipo' => $loc->getTipo()];

        $data = $this->getJson($request);
        $dto = new LocationUpdateDto();
        $dto->nombre = $data['nombre'] ?? null;
        $dto->tipo = $data['tipo'] ?? null;
        $dto->capacidadLitros = $data['capacidadLitros'] ?? null;
        $this->validateDto($dto);

        if ($dto->nombre !== null) {
            $loc->setNombre($dto->nombre);
        }
        if ($dto->tipo !== null) {
            $loc->setTipo($dto->tipo);
        }
        if ($dto->capacidadLitros !== null) {
            $loc->setCapacidadLitros((string) $dto->capacidadLitros);
        }

        $this->em->flush();

        $actor = $this->getUser();
        $this->audit->log('ubicaciones', (string) $loc->getId(), 'update', $before, ['nombre' => $loc->getNombre(), 'tipo' => $loc->getTipo()], $actor?->getId());

        return $this->jsonOk($loc);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $loc = $this->em->getRepository(Location::class)->find($id);
        if (!$loc) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->em->remove($loc);
        $this->em->flush();
        return $this->jsonOk(['deleted' => true]);
    }
}
