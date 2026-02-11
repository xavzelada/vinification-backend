<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class LoginDto
{
    #[Assert\Email]
    #[Assert\NotBlank]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $password;
}
