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
        $dto->fechaHora = (string) ($data['fechaHora'] ?? ($data['fecha'] ?? ''));
        $dto->nariz = $data['nariz'] ?? null;
        $dto->boca = $data['boca'] ?? null;
        $dto->color = $data['color'] ?? null;
        $dto->defectos = $data['defectos'] ?? null;
        $dto->comentario = $data['comentario'] ?? ($data['notasLibres'] ?? null);
        $this->validateDto($dto);
        $this->validateOrganolepticPayload($dto->color, $dto->nariz, $dto->boca, $dto->defectos);

        $lote = $this->em->getRepository(Batch::class)->find($dto->loteId);
        if (!$lote) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Lote no encontrado');
        }
        $this->assertTenantBodegaId($lote->getBodega()->getId());
        $user = $this->getUser();

        $org = new Organoleptic();
        $org->setLote($lote)
            ->setUsuario($user)
            ->setFechaHora(new \DateTimeImmutable($dto->fechaHora))
            ->setNariz($dto->nariz)
            ->setBoca($dto->boca)
            ->setColor($dto->color)
            ->setDefectos($dto->defectos)
            ->setNotasLibres($dto->comentario);

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
        $dto->comentario = $data['comentario'] ?? ($data['notasLibres'] ?? null);
        $this->validateDto($dto);
        $this->validateOrganolepticPayload($dto->color, $dto->nariz, $dto->boca, $dto->defectos);

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
        if ($dto->comentario !== null) {
            $org->setNotasLibres($dto->comentario);
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

    private function validateOrganolepticPayload(?array $color, ?array $nariz, ?array $boca, ?array $defectos): void
    {
        if ($color !== null) {
            $this->validateRangeField($color, 'intensidad', 1, 5, 'color.intensidad');
        }
        if ($nariz !== null) {
            $this->validateRangeField($nariz, 'intensidad', 1, 5, 'nariz.intensidad');
            if (isset($nariz['notas']) && is_array($nariz['notas'])) {
                $allowed = ['fruta', 'floral', 'especias', 'madera', 'reduccion', 'oxidacion', 'otros'];
                foreach ($nariz['notas'] as $nota) {
                    if (!is_string($nota)) {
                        throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('nariz.notas debe ser lista de textos');
                    }
                    if (!in_array($this->normalizeNote($nota), $allowed, true)) {
                        throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('nariz.notas contiene valor no permitido');
                    }
                }
            }
        }
        if ($boca !== null) {
            $this->validateRangeField($boca, 'acidez', 1, 5, 'boca.acidez');
            $this->validateRangeField($boca, 'tanino', 1, 5, 'boca.tanino');
            $this->validateRangeField($boca, 'alcohol', 1, 5, 'boca.alcohol');
            $this->validateRangeField($boca, 'cuerpo', 1, 5, 'boca.cuerpo');
            $this->validateRangeField($boca, 'persistencia', 1, 5, 'boca.persistencia');
        }
        if ($defectos !== null) {
            if (!is_array($defectos)) {
                throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('defectos debe ser lista');
            }
            foreach ($defectos as $defecto) {
                if (!is_string($defecto)) {
                    throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('defectos debe ser lista de textos');
                }
            }
        }
    }

    private function validateRangeField(array $data, string $field, int $min, int $max, string $label): void
    {
        if (!array_key_exists($field, $data)) {
            return;
        }
        $value = $data[$field];
        if (!is_numeric($value)) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException($label . ' debe ser numerico');
        }
        $value = (int) $value;
        if ($value < $min || $value > $max) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException($label . ' fuera de rango');
        }
    }

    private function normalizeNote(string $note): string
    {
        $note = strtolower($note);
        return strtr($note, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ñ' => 'n',
        ]);
    }
}
