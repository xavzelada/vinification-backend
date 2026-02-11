<?php

namespace App\Controller;

use App\Dto\AlertRuleCreateDto;
use App\Dto\AlertRuleUpdateDto;
use App\Entity\AlertRule;
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

#[Route('/alert-rules')]
class AlertRulesController extends ApiController
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
            $items = $this->em->getRepository(AlertRule::class)->findAll();
        } else {
            $items = $this->em->getRepository(AlertRule::class)->findBy([
                'bodega' => $this->getActor()->getBodega()
            ]);
        }
        return $this->jsonOk($items);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getOne(int $id): Response
    {
        $item = $this->em->getRepository(AlertRule::class)->find($id);
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
        $dto = new AlertRuleCreateDto();
        $dto->bodegaId = (int) ($data['bodegaId'] ?? 0);
        $dto->etapaId = (int) ($data['etapaId'] ?? 0);
        $dto->nombre = (string) ($data['nombre'] ?? '');
        $dto->parametro = (string) ($data['parametro'] ?? '');
        $dto->operador = (string) ($data['operador'] ?? '');
        $dto->valor = $data['valor'] ?? null;
        $dto->valorMax = $data['valorMax'] ?? null;
        $dto->periodoDias = $data['periodoDias'] ?? null;
        $dto->severidad = (string) ($data['severidad'] ?? 'info');
        $dto->activa = $data['activa'] ?? true;
        $dto->descripcion = $data['descripcion'] ?? null;
        $this->validateDto($dto);

        $bodega = $this->em->getRepository(Bodega::class)->find($dto->bodegaId);
        $etapa = $this->em->getRepository(Stage::class)->find($dto->etapaId);
        if (!$bodega || !$etapa) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Bodega o etapa no encontrada');
        }
        $this->assertTenantBodegaId($bodega->getId());

        $rule = new AlertRule();
        $rule->setBodega($bodega)
            ->setEtapa($etapa)
            ->setNombre($dto->nombre)
            ->setParametro($dto->parametro)
            ->setOperador($dto->operador)
            ->setValor($dto->valor !== null ? (string) $dto->valor : null)
            ->setValorMax($dto->valorMax !== null ? (string) $dto->valorMax : null)
            ->setPeriodoDias($dto->periodoDias)
            ->setSeveridad($dto->severidad)
            ->setActiva((bool) $dto->activa)
            ->setDescripcion($dto->descripcion);

        $this->em->persist($rule);
        $this->em->flush();

        $actor = $this->getUser();
        $this->audit->log('alert_rules', (string) $rule->getId(), 'create', null, ['nombre' => $rule->getNombre()], $actor?->getId());

        return $this->jsonOk($rule, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_ENOLOGO')]
    public function update(int $id, Request $request): Response
    {
        $rule = $this->em->getRepository(AlertRule::class)->find($id);
        if (!$rule) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($rule->getBodega()->getId());

        $data = $this->getJson($request);
        $dto = new AlertRuleUpdateDto();
        $dto->nombre = $data['nombre'] ?? null;
        $dto->parametro = $data['parametro'] ?? null;
        $dto->operador = $data['operador'] ?? null;
        $dto->valor = $data['valor'] ?? null;
        $dto->valorMax = $data['valorMax'] ?? null;
        $dto->periodoDias = $data['periodoDias'] ?? null;
        $dto->severidad = $data['severidad'] ?? null;
        $dto->activa = $data['activa'] ?? null;
        $dto->descripcion = $data['descripcion'] ?? null;
        $this->validateDto($dto);

        if ($dto->nombre !== null) {
            $rule->setNombre($dto->nombre);
        }
        if ($dto->parametro !== null) {
            $rule->setParametro($dto->parametro);
        }
        if ($dto->operador !== null) {
            $rule->setOperador($dto->operador);
        }
        if ($dto->valor !== null) {
            $rule->setValor((string) $dto->valor);
        }
        if ($dto->valorMax !== null) {
            $rule->setValorMax((string) $dto->valorMax);
        }
        if ($dto->periodoDias !== null) {
            $rule->setPeriodoDias($dto->periodoDias);
        }
        if ($dto->severidad !== null) {
            $rule->setSeveridad($dto->severidad);
        }
        if ($dto->activa !== null) {
            $rule->setActiva((bool) $dto->activa);
        }
        if ($dto->descripcion !== null) {
            $rule->setDescripcion($dto->descripcion);
        }

        $this->em->flush();
        return $this->jsonOk($rule);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $rule = $this->em->getRepository(AlertRule::class)->find($id);
        if (!$rule) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($rule->getBodega()->getId());
        $this->em->remove($rule);
        $this->em->flush();
        return $this->jsonOk(['deleted' => true]);
    }
}
