<?php

namespace App\Controller;

use App\Entity\Recommendation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/recomendaciones')]
class RecommendationsController extends ApiController
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
            $items = $this->em->getRepository(Recommendation::class)->findAll();
        } else {
            $qb = $this->em->createQueryBuilder();
            $items = $qb->select('r')
                ->from(Recommendation::class, 'r')
                ->join('r.lote', 'l')
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
        $item = $this->em->getRepository(Recommendation::class)->find($id);
        if (!$item) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->assertTenantBodegaId($item->getLote()->getBodega()->getId());
        return $this->jsonOk($item);
    }
}
