<?php

namespace App\Controller;

use App\Dto\LoginDto;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthController extends ApiController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager
    ) {
        parent::__construct($serializer, $validator);
    }

    #[Route('/auth/login', name: 'auth_login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $data = $this->getJson($request);
        $dto = new LoginDto();
        $dto->email = $data['email'] ?? '';
        $dto->password = $data['password'] ?? '';
        $this->validateDto($dto);

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $dto->email]);
        if (!$user || !$this->passwordHasher->isPasswordValid($user, $dto->password) || !$user->isActivo()) {
            throw new UnauthorizedHttpException('', 'Credenciales invalidas');
        }

        $token = $this->jwtManager->create($user);

        return $this->json([
            'accessToken' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'bodegaId' => $user->getBodega()->getId()
            ]
        ]);
    }
}
