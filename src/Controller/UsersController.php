<?php

namespace App\Controller;

use App\Dto\UserCreateDto;
use App\Dto\UserUpdateDto;
use App\Entity\Bodega;
use App\Entity\User;
use App\Enum\UserRole;
use App\Service\AuditService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/usuarios')]
class UsersController extends ApiController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
        private AuditService $audit
    ) {
        parent::__construct($serializer, $validator);
    }

    #[Route('', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function list(): Response
    {
        $items = $this->em->getRepository(User::class)->findAll();
        return $this->jsonOk($items);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getOne(int $id): Response
    {
        $item = $this->em->getRepository(User::class)->find($id);
        if (!$item) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        return $this->jsonOk($item);
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $data = $this->getJson($request);
        $dto = new UserCreateDto();
        $dto->email = (string) ($data['email'] ?? '');
        $dto->nombre = (string) ($data['nombre'] ?? '');
        $dto->password = (string) ($data['password'] ?? '');
        $dto->role = (string) ($data['role'] ?? UserRole::LECTURA);
        $dto->bodegaId = (int) ($data['bodegaId'] ?? 0);
        $this->validateDto($dto);

        $bodega = $this->em->getRepository(Bodega::class)->find($dto->bodegaId);
        if (!$bodega) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Bodega no encontrada');
        }
        $user = new User();
        $user->setEmail($dto->email)
            ->setNombre($dto->nombre)
            ->setRoles([$dto->role])
            ->setBodega($bodega)
            ->setPassword($this->hasher->hashPassword($user, $dto->password));

        $this->em->persist($user);
        $this->em->flush();

        $actor = $this->getUser();
        $this->audit->log('usuarios', (string) $user->getId(), 'create', null, ['email' => $user->getEmail()], $actor?->getId());

        return $this->jsonOk($user, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, Request $request): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if (!$user) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $before = ['nombre' => $user->getNombre(), 'roles' => $user->getRoles(), 'activo' => $user->isActivo()];

        $data = $this->getJson($request);
        $dto = new UserUpdateDto();
        $dto->nombre = $data['nombre'] ?? null;
        $dto->role = $data['role'] ?? null;
        $dto->password = $data['password'] ?? null;
        $dto->activo = $data['activo'] ?? null;
        $this->validateDto($dto);

        if ($dto->nombre !== null) {
            $user->setNombre($dto->nombre);
        }
        if ($dto->role !== null) {
            $user->setRoles([$dto->role]);
        }
        if ($dto->password !== null) {
            $user->setPassword($this->hasher->hashPassword($user, $dto->password));
        }
        if ($dto->activo !== null) {
            $user->setActivo($dto->activo);
        }
        $this->em->flush();

        $actor = $this->getUser();
        $this->audit->log('usuarios', (string) $user->getId(), 'update', $before, ['nombre' => $user->getNombre(), 'roles' => $user->getRoles(), 'activo' => $user->isActivo()], $actor?->getId());

        return $this->jsonOk($user);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if (!$user) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No encontrado');
        }
        $this->em->remove($user);
        $this->em->flush();
        return $this->jsonOk(['deleted' => true]);
    }
}
