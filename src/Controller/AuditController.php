<?php

namespace App\Controller;

use App\Entity\AuditLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/audit')]
class AuditController extends ApiController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        private EntityManagerInterface $em
    ) {
        parent::__construct($serializer, $validator);
    }

    #[Route('', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function list(): Response
    {
        $items = $this->em->getRepository(AuditLog::class)->findBy([], ['id' => 'DESC']);
        return $this->jsonOk($items);
    }
}
