<?php

namespace App\Dto;

use App\Enum\UserRole;
use Symfony\Component\Validator\Constraints as Assert;

class UserUpdateDto
{
    public ?string $nombre = null;

    #[Assert\Choice(choices: UserRole::ALL)]
    public ?string $role = null;

    #[Assert\Length(min: 6)]
    public ?string $password = null;

    public ?bool $activo = null;
}