<?php

namespace App\Service\Twig;

use App\Entity\User;
use App\Repository\MenuRepository;

readonly class MenuService
{
    public function __construct(
        private MenuRepository $menuRepository,
    ) {
    }

    public function getAll(User $user): array
    {
        return $this->menuRepository->findBy(
            ['parent' => null, 'isActive' => true, 'isVisible' => true],
            ['position' => 'ASC']
        );
    }
}