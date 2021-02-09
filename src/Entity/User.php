<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"username"}, groups={"user:write", "user:register"})
 * @UniqueEntity(fields={"email"}, groups={"user:write", "user:register"})
 */
#[ApiResource(
    collectionOperations: [
        "register" => [
            "method" => "POST",
            "path" => "/users/register",
            "denormalization_context" => ["groups" => ["user:register"]],
            "validation_groups" => ["user:register"],
            "input" => "App\Dto\UserRegisterInput",
            "swagger_context" => [
                "summary" => "Register user"
            ]
        ]
    ],
    itemOperations: [
        "get" => [
            "security" => "is_granted('ROLE_ADMIN') or (object == user && object.active)",
        ],
        "changePassword" => [
            "method" => "PUT",
            "path" => "/users/{id}/change-password",
            "security" => "is_granted('ROLE_ADMIN') or object == user",
            "denormalization_context" => ["groups" => ["user:changePassword"]],
            "validation_groups" => ["user:changePassword"],
            "swagger_context" => [
                "summary" => "Change user password"
            ]
        ],
        "activate" => [
            "method" => "PUT",
            "path" => "/users/{id}/activate",
            "denormalization_context" => ["groups" => ["user:activate"]],
            "validation_groups" => ["user:changePassword"],
            "swagger_context" => [
                "summary" => "Change user password"
            ]
        ],
    ],
    denormalizationContext: ['groups' => ["user:write"]],
    normalizationContext: ['groups' => ["user:read"]]
)]
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @ORM\Column(type="uuid", unique=true)
     */
    private ?UuidInterface $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:read", "user:write", "user:register"})
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"user:read", "user:register"})
     * @Assert\NotBlank()
     */
    private ?string $username;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     */
    private ?string $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:register", "user:activate"})
     */
    private ?string $token;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:read"})
     */
    private bool $active = false;

    /**
     * @Groups({"user:write", "user:changePassword"})
     * @SerializedName("password")
     * @Assert\NotBlank(groups={"user:changePassword", "user:register"})
     * @Assert\Length(min="5", max="255", groups={"user:write", "user:changePassword"})
     */
    private ?string $plainPassword = null;

    /**
     * @Groups({"user:changePassword"})
     * @Assert\NotBlank(groups={"user:changePassword"})
     * @SecurityAssert\UserPassword(message = "Wrong value for your current password", groups={"user:changePassword"})
     */
    private ?string $oldPassword = null;

    public function __construct(string $username, string $email, string $plainPassword, string $token)
    {
        $this->username = $username;
        $this->email = $email;
        $this->plainPassword = $plainPassword;
        $this->token = $token;
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;
        return $this;
    }
}
