<?php

namespace App\Controller;

use App\Dto\RecommendationRuleCreateDto;
use App\Dto\RecommendationRuleUpdateDto;
use App\Entity\Bodega;
use App\Entity\Product;
use App\Entity\RecommendationRule;
use App\Entity\Stage;
use App\Service\AuditService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/recommendation-rules')]
class RecommendationRulesController extends ApiController
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
            $items = $this->em->getRepository(RecommendationRule::class)->findAll();
        } else {
            $items = $this->em->getRepository(RecommendationRule::class)->findBy([
                'bodega' => $this->getActor()->getBodega()
            ]);
        }
        return $this->jsonOk($items);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getOne(int $id): Response
    {
        $item = $this->em->getRepository(RecommendationRule::class)->find($id);
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
        $dto = new RecommendationRuleCreateDto();
        $dto->bodegaId = (int) ($data['bodegaId'] ?? 0);
        $dto->etapaId = (int) ($data['etapaId'] ?? 0);
        $dto->nombre = (string) ($data['nombre'] ?? '');
        $dto->condiciones = $data['condiciones'] ?? [];
        $dto->accionSugerida = (string) ($data['accionSugerida'] ?? '');
        $dto->productoId = $data['productoId'] ?? null;
        $dto->dosisSugerida = $data['dosisSugerida'] ?? null;
        $dto->unidad = $data['unidad'] ?? null;
        $dto->explicacion = $data['explicacion'] ?? null;
        $dto->activa = $data['activa'] ?? true;
        $this->validateDto($dto);

        $bodega = $this->em->getRepository(Bodega::class)->find($dto->bodegaId);
        $etapa = $this->em->getRepository(Stage::class)->find($dto->etapaId);
        if (!$bodega || !$etapa) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Bodega o etapa no encontrada');
        }
        $this->assertTenantBodegaId($bodega->getId());

        $producto = null;
        if ($dto->productoId) {
            $producto = $this->em->getRepository(Product::class)->find((int) $dto->productoId);
            if ($producto) {
                $this->assertTenantBodegaId($producto->getBodega()->getId());
            }
        }

        $rule = new RecommendationRule();
        $rule->setBodega($bodega)
            ->setEtapa($etapa)
            ->setNombre($dto->nombre)
            ->setCondiciones($dto->condiciones)
            ->setAccionSugerida($dto->accionSugerida)
            ->setProducto($producto)
            ->setDosisSugerida($dto->dosisSugerida !== null ? (string) $dto->dosisSugerida : null)
            ->setUnidad($dto->unidad)
            ->setExplicacion($dto->explicacion)
            ->setActiva((bool) $dto->activa);

        $this->em->persist($rule);
        $this->em->flush();

        $actor = $this->getUser();
        $this->audit->log('recommendation_rules', (string) $rule->getId(), 'create', null, ['nombre' => $rule->getNombre()], $actor?->getId());

        return $this->jsonOk($rule, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_ENOLOGO')]
    public function update(int $id, Request $request): Response
    {
        $rule = $this->em->getRepository(RecommendationRule::class)->find($id);
        if (!$rule) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($rule->getBodega()->getId());
        $data = $this->getJson($request);
        $dto = new RecommendationRuleUpdateDto();
        $dto->nombre = $data['nombre'] ?? null;
        $dto->condiciones = $data['condiciones'] ?? null;
        $dto->accionSugerida = $data['accionSugerida'] ?? null;
        $dto->productoId = $data['productoId'] ?? null;
        $dto->dosisSugerida = $data['dosisSugerida'] ?? null;
        $dto->unidad = $data['unidad'] ?? null;
        $dto->explicacion = $data['explicacion'] ?? null;
        $dto->activa = $data['activa'] ?? null;
        $this->validateDto($dto);

        if ($dto->nombre !== null) {
            $rule->setNombre($dto->nombre);
        }
        if ($dto->condiciones !== null) {
            $rule->setCondiciones($dto->condiciones);
        }
        if ($dto->accionSugerida !== null) {
            $rule->setAccionSugerida($dto->accionSugerida);
        }
        if ($dto->productoId !== null) {
            $producto = $this->em->getRepository(Product::class)->find((int) $dto->productoId);
            if ($producto) {
                $this->assertTenantBodegaId($producto->getBodega()->getId());
            }
            $rule->setProducto($producto);
        }
        if ($dto->dosisSugerida !== null) {
            $rule->setDosisSugerida((string) $dto->dosisSugerida);
        }
        if ($dto->unidad !== null) {
            $rule->setUnidad($dto->unidad);
        }
        if ($dto->explicacion !== null) {
            $rule->setExplicacion($dto->explicacion);
        }
        if ($dto->activa !== null) {
            $rule->setActiva((bool) $dto->activa);
        }
        $this->em->flush();
        return $this->jsonOk($rule);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $rule = $this->em->getRepository(RecommendationRule::class)->find($id);
        if (!$rule) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($rule->getBodega()->getId());
        $this->em->remove($rule);
        $this->em->flush();
        return $this->jsonOk(['deleted' => true]);
    }
}
