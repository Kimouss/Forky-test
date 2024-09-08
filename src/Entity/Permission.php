<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
    public const UNCHECK_ROUTES = [
        'app_login',
        'app_logout',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Profile $profile = null;

    #[ORM\Column(length: 255)]
    private ?string $baseRoute = null;

    #[ORM\Column]
    private array $access = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }

    public function getBaseRoute(): ?string
    {
        return $this->baseRoute;
    }

    public function setBaseRoute(string $baseRoute): static
    {
        $this->baseRoute = $baseRoute;

        return $this;
    }

    public function getAccess(): array
    {
        return $this->access;
    }

    public function setAccess(array $access): static
    {
        $this->access = $access;

        return $this;
    }
}
