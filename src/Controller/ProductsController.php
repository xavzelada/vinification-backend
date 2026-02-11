<?php

namespace App\Controller;

use App\Dto\ProductCreateDto;
use App\Dto\ProductUpdateDto;
use App\Entity\Bodega;
use App\Entity\Product;
use App\Service\AuditService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/productos')]
class ProductsController extends ApiController
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
            $items = $this->em->getRepository(Product::class)->findAll();
        } else {
            $items = $this->em->getRepository(Product::class)->findBy([
                'bodega' => $this->getActor()->getBodega()
            ]);
        }
        return $this->jsonOk($items);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getOne(int $id): Response
    {
        $item = $this->em->getRepository(Product::class)->find($id);
        if (!$item) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($item->getBodega()->getId());
        return $this->jsonOk($item);
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_ENOLOGO')]
    public function create(Request $request): Response
    {
        $data = $this->getJson($request);
        $dto = new ProductCreateDto();
        $dto->bodegaId = (int) ($data['bodegaId'] ?? 0);
        $dto->nombre = (string) ($data['nombre'] ?? '');
        $dto->categoria = (string) ($data['categoria'] ?? '');
        $dto->descripcion = $data['descripcion'] ?? null;
        $dto->unidad = (string) ($data['unidad'] ?? '');
        $dto->rangoDosisMin = $data['rangoDosisMin'] ?? null;
        $dto->rangoDosisMax = $data['rangoDosisMax'] ?? null;
        $dto->notas = $data['notas'] ?? null;
        $this->validateDto($dto);

        $bodega = $this->em->getRepository(Bodega::class)->find($dto->bodegaId);
        if (!$bodega) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Bodega no encontrada');
        }
        $this->assertTenantBodegaId($bodega->getId());

        $product = new Product();
        $product->setBodega($bodega)
            ->setNombre($dto->nombre)
            ->setCategoria($dto->categoria)
            ->setDescripcion($dto->descripcion)
            ->setUnidad($dto->unidad)
            ->setRangoDosisMin($dto->rangoDosisMin !== null ? (string) $dto->rangoDosisMin : null)
            ->setRangoDosisMax($dto->rangoDosisMax !== null ? (string) $dto->rangoDosisMax : null)
            ->setNotas($dto->notas);

        $this->em->persist($product);
        $this->em->flush();

        $actor = $this->getUser();
        $this->audit->log('productos', (string) $product->getId(), 'create', null, ['nombre' => $product->getNombre()], $actor?->getId());

        return $this->jsonOk($product, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_ENOLOGO')]
    public function update(int $id, Request $request): Response
    {
        $product = $this->em->getRepository(Product::class)->find($id);
        if (!$product) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($product->getBodega()->getId());

        $data = $this->getJson($request);
        $dto = new ProductUpdateDto();
        $dto->nombre = $data['nombre'] ?? null;
        $dto->categoria = $data['categoria'] ?? null;
        $dto->descripcion = $data['descripcion'] ?? null;
        $dto->unidad = $data['unidad'] ?? null;
        $dto->rangoDosisMin = $data['rangoDosisMin'] ?? null;
        $dto->rangoDosisMax = $data['rangoDosisMax'] ?? null;
        $dto->notas = $data['notas'] ?? null;
        $dto->activo = $data['activo'] ?? null;
        $this->validateDto($dto);

        if ($dto->nombre !== null) {
            $product->setNombre($dto->nombre);
        }
        if ($dto->categoria !== null) {
            $product->setCategoria($dto->categoria);
        }
        if ($dto->descripcion !== null) {
            $product->setDescripcion($dto->descripcion);
        }
        if ($dto->unidad !== null) {
            $product->setUnidad($dto->unidad);
        }
        if ($dto->rangoDosisMin !== null) {
            $product->setRangoDosisMin((string) $dto->rangoDosisMin);
        }
        if ($dto->rangoDosisMax !== null) {
            $product->setRangoDosisMax((string) $dto->rangoDosisMax);
        }
        if ($dto->notas !== null) {
            $product->setNotas($dto->notas);
        }
        if ($dto->activo !== null) {
            $product->setActivo($dto->activo);
        }

        $this->em->flush();
        return $this->jsonOk($product);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $product = $this->em->getRepository(Product::class)->find($id);
        if (!$product) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->em->remove($product);
        $this->em->flush();
        return $this->jsonOk(['deleted' => true]);
    }
}
