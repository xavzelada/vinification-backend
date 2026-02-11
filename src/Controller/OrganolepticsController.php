<?php

namespace App\Controller;

use App\Dto\OrganolepticCreateDto;
use App\Dto\OrganolepticUpdateDto;
use App\Entity\Batch;
use App\Entity\Organoleptic;
use App\Service\AuditService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/organoleptics')]
class OrganolepticsController extends ApiController
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
            $items = $this->em->getRepository(Organoleptic::class)->findAll();
        } else {
            $qb = $this->em->createQueryBuilder();
            $items = $qb->select('o')
                ->from(Organoleptic::class, 'o')
                ->join('o.lote', 'l')
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
        $item = $this->em->getRepository(Organoleptic::class)->find($id);
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
        $dto = new OrganolepticCreateDto();
        $dto->loteId = (int) ($data['loteId'] ?? 0);
        $dto->fecha = (string) ($data['fecha'] ?? '');
        $dto->nariz = $data['nariz'] ?? null;
        $dto->boca = $data['boca'] ?? null;
        $dto->color = $data['color'] ?? null;
        $dto->defectos = $data['defectos'] ?? null;
        $dto->intensidad = $data['intensidad'] ?? null;
        $dto->notasLibres = $data['notasLibres'] ?? null;
        $this->validateDto($dto);

        $lote = $this->em->getRepository(Batch::class)->find($dto->loteId);
        if (!$lote) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Lote no encontrado');
        }
        $this->assertTenantBodegaId($lote->getBodega()->getId());
        $user = $this->getUser();

        $org = new Organoleptic();
        $org->setLote($lote)
            ->setUsuario($user)
            ->setFecha(new \DateTimeImmutable($dto->fecha))
            ->setNariz($dto->nariz)
            ->setBoca($dto->boca)
            ->setColor($dto->color)
            ->setDefectos($dto->defectos)
            ->setIntensidad($dto->intensidad)
            ->setNotasLibres($dto->notasLibres);

        $this->em->persist($org);
        $this->em->flush();

        $this->audit->log('organolepticas', (string) $org->getId(), 'create', null, ['loteId' => $lote->getId()], $user?->getId());

        return $this->jsonOk($org, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_ENOLOGO')]
    public function update(int $id, Request $request): Response
    {
        $org = $this->em->getRepository(Organoleptic::class)->find($id);
        if (!$org) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($org->getLote()->getBodega()->getId());
        $data = $this->getJson($request);
        $dto = new OrganolepticUpdateDto();
        $dto->nariz = $data['nariz'] ?? null;
        $dto->boca = $data['boca'] ?? null;
        $dto->color = $data['color'] ?? null;
        $dto->defectos = $data['defectos'] ?? null;
        $dto->intensidad = $data['intensidad'] ?? null;
        $dto->notasLibres = $data['notasLibres'] ?? null;
        $this->validateDto($dto);

        if ($dto->nariz !== null) {
            $org->setNariz($dto->nariz);
        }
        if ($dto->boca !== null) {
            $org->setBoca($dto->boca);
        }
        if ($dto->color !== null) {
            $org->setColor($dto->color);
        }
        if ($dto->defectos !== null) {
            $org->setDefectos($dto->defectos);
        }
        if ($dto->intensidad !== null) {
            $org->setIntensidad($dto->intensidad);
        }
        if ($dto->notasLibres !== null) {
            $org->setNotasLibres($dto->notasLibres);
        }
        $this->em->flush();
        return $this->jsonOk($org);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $org = $this->em->getRepository(Organoleptic::class)->find($id);
        if (!$org) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($org->getLote()->getBodega()->getId());
        $this->em->remove($org);
        $this->em->flush();
        return $this->jsonOk(['deleted' => true]);
    }
}
