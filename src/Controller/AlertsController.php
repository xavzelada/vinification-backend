<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Enum\AlertStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/alertas')]
class AlertsController extends ApiController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        private EntityManagerInterface $em
    ) {
        parent::__construct($serializer, $validator);
    }

    #[Route('', methods: ['GET'])]
    public function list(): Response
    {
        if ($this->isAdmin()) {
            $items = $this->em->getRepository(Alert::class)->findAll();
        } else {
            $qb = $this->em->createQueryBuilder();
            $items = $qb->select('a')
                ->from(Alert::class, 'a')
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
        $item = $this->em->getRepository(Alert::class)->find($id);
        if (!$item) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($item->getLote()->getBodega()->getId());
        return $this->jsonOk($item);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_OPERADOR')]
    public function update(int $id, Request $request): Response
    {
        $alert = $this->em->getRepository(Alert::class)->find($id);
        if (!$alert) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($alert->getLote()->getBodega()->getId());
        $data = $this->getJson($request);
        $estado = $data['estado'] ?? null;
        if ($estado) {
            $alert->setEstado($estado);
            if ($estado === AlertStatus::ACK) {
                $alert->setAckAt(new \DateTimeImmutable());
            }
            if ($estado === AlertStatus::RESOLVED) {
                $alert->setResolvedAt(new \DateTimeImmutable());
            }
        }
        $this->em->flush();
        return $this->jsonOk($alert);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $alert = $this->em->getRepository(Alert::class)->find($id);
        if (!$alert) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($alert->getLote()->getBodega()->getId());
        $this->em->remove($alert);
        $this->em->flush();
        return $this->jsonOk(['deleted' => true]);
    }
}
