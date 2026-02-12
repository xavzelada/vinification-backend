<?php

namespace App\Dto;

use App\Enum\UserRole;
use Symfony\Component\Validator\Constraints as Assert;

class UserCreateDto
{
    #[Assert\Email]
    #[Assert\NotBlank]
    public string $email;

    #[Assert\NotBlank]
    public string $nombre;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $password;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: UserRole::ALL)]
    public string $role;

    #[Assert\NotBlank]
    public int $bodegaId;
}