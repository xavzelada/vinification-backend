<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class ApiController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {
    }

    protected function getJson(Request $request): array
    {
        $content = $request->getContent();
        if ($content === '') {
            return [];
        }
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    protected function validateDto(object $dto): void
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            throw new BadRequestHttpException(implode('; ', $messages));
        }
    }

    protected function jsonOk(mixed $data, int $status = 200, array $context = []): JsonResponse
    {
        $defaultContext = [
            'circular_reference_handler' => function ($object) {
                if (is_object($object) && method_exists($object, 'getId')) {
                    return $object->getId();
                }
                return null;
            }
        ];
        return $this->json($data, $status, [], array_merge($defaultContext, $context));
    }

    protected function getActor(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new NotFoundHttpException('Usuario no autenticado');
        }
        return $user;
    }

    protected function isAdmin(): bool
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return false;
        }
        return in_array(UserRole::ADMIN, $user->getRoles(), true);
    }

    protected function getActorBodegaId(): int
    {
        $user = $this->getActor();
        return $user->getBodega()->getId();
    }

    protected function assertTenantBodegaId(int $bodegaId): void
    {
        if ($this->isAdmin()) {
            return;
        }
        if ($bodegaId !== $this->getActorBodegaId()) {
            throw new NotFoundHttpException('No encontrado');
        }
    }
}
