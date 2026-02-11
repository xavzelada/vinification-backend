<?php

namespace App\Dto;

use App\Enum\UserRole;
use Symfony\Component\Validator\Constraints as Assert;

class UserUpdateDto
{
    #[Assert\Optional]
    public ?string $nombre = null;

    #[Assert\Optional]
    #[Assert\Choice(choices: UserRole::ALL)]
    public ?string $role = null;

    #[Assert\Optional]
    #[Assert\Length(min: 6)]
    public ?string $password = null;

    #[Assert\Optional]
    public ?bool $activo = null;
}
