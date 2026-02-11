<?php

namespace App\Controller;

use App\Dto\BodegaCreateDto;
use App\Dto\BodegaUpdateDto;
use App\Entity\Bodega;
use App\Service\AuditService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/bodegas')]
class BodegasController extends ApiController
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
            $items = $this->em->getRepository(Bodega::class)->findAll();
        } else {
            $items = [$this->getActor()->getBodega()];
        }
        return $this->jsonOk($items);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getOne(int $id): Response
    {
        $item = $this->em->getRepository(Bodega::class)->find($id);
        if (!$item) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($item->getId());
        return $this->jsonOk($item);
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $data = $this->getJson($request);
        $dto = new BodegaCreateDto();
        $dto->codigo = (string) ($data['codigo'] ?? '');
        $dto->nombre = (string) ($data['nombre'] ?? '');
        $dto->pais = $data['pais'] ?? null;
        $this->validateDto($dto);

        $bodega = new Bodega();
        $bodega->setCodigo($dto->codigo)
            ->setNombre($dto->nombre)
            ->setPais($dto->pais);

        $this->em->persist($bodega);
        $this->em->flush();

        $user = $this->getUser();
        $this->audit->log('bodegas', (string) $bodega->getId(), 'create', null, ['codigo' => $dto->codigo], $user?->getId());

        return $this->jsonOk($bodega, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, Request $request): Response
    {
        $bodega = $this->em->getRepository(Bodega::class)->find($id);
        if (!$bodega) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $before = ['nombre' => $bodega->getNombre(), 'pais' => $bodega->getPais()];

        $data = $this->getJson($request);
        $dto = new BodegaUpdateDto();
        $dto->nombre = $data['nombre'] ?? null;
        $dto->pais = $data['pais'] ?? null;
        $this->validateDto($dto);

        if ($dto->nombre !== null) {
            $bodega->setNombre($dto->nombre);
        }
        if ($dto->pais !== null) {
            $bodega->setPais($dto->pais);
        }
        $this->em->flush();

        $user = $this->getUser();
        $this->audit->log('bodegas', (string) $bodega->getId(), 'update', $before, ['nombre' => $bodega->getNombre(), 'pais' => $bodega->getPais()], $user?->getId());

        return $this->jsonOk($bodega);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $bodega = $this->em->getRepository(Bodega::class)->find($id);
        if (!$bodega) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->em->remove($bodega);
        $this->em->flush();
        return $this->jsonOk(['deleted' => true]);
    }
}
