<?php

namespace App\Controller;

use App\Dto\MeasurementUpdateDto;
use App\Entity\Measurement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/measurements')]
class MeasurementsController extends ApiController
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
            $items = $this->em->getRepository(Measurement::class)->findAll();
        } else {
            $qb = $this->em->createQueryBuilder();
            $items = $qb->select('m')
                ->from(Measurement::class, 'm')
                ->join('m.lote', 'l')
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
        $item = $this->em->getRepository(Measurement::class)->find($id);
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
        $measurement = $this->em->getRepository(Measurement::class)->find($id);
        if (!$measurement) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($measurement->getLote()->getBodega()->getId());
        $data = $this->getJson($request);
        $dto = new MeasurementUpdateDto();
        $dto->densidad = $data['densidad'] ?? null;
        $dto->temperaturaC = $data['temperaturaC'] ?? null;
        $dto->brix = $data['brix'] ?? null;
        $dto->comentario = $data['comentario'] ?? null;
        $dto->fechaHora = $data['fechaHora'] ?? null;
        $this->validateDto($dto);

        if ($dto->densidad !== null) {
            $measurement->setDensidad((string) $dto->densidad);
        }
        if ($dto->temperaturaC !== null) {
            $measurement->setTemperaturaC((string) $dto->temperaturaC);
        }
        if ($dto->brix !== null) {
            $measurement->setBrix((string) $dto->brix);
        }
        if ($dto->comentario !== null) {
            $measurement->setComentario($dto->comentario);
        }
        if ($dto->fechaHora !== null) {
            $measurement->setFechaHora(new \DateTimeImmutable($dto->fechaHora));
        }
        $this->em->flush();
        return $this->jsonOk($measurement);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $item = $this->em->getRepository(Measurement::class)->find($id);
        if (!$item) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($item->getLote()->getBodega()->getId());
        $this->em->remove($item);
        $this->em->flush();
        return $this->jsonOk(['deleted' => true]);
    }

}
