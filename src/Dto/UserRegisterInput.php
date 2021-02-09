<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AppValidator;

#[ApiResource(
    input: UserRegisterInput::CLASS,
)]
final class UserRegisterInput
{
    /**
     * @Groups({"user:register"})
     * @Assert\NotBlank
     */
    public string $username;

    /**
     * @Groups({"user:register"})
     * @Assert\NotBlank
     */
    public string $email;

    /**
     * @Groups({"user:register"})
     * @Assert\NotBlank
     * @Assert\Length(min="5", max="255")
     */
    public string $password;

    /**
     * @Groups({"user:register"})
     * @Assert\NotBlank
     * @AppValidator\Recaptcha
     */
    public string $captcha;
}
