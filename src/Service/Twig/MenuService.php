<?php

namespace App\Service\Twig;

use App\Repository\MenuRepository;

readonly class MenuService
{
    public function __construct(
        private MenuRepository $menuRepository,
    )
    {
    }

    public function getAll(): array
    {
        return $this->menuRepository->findBy(
            ['parent' => null, 'isActive' => true],
            ['position' => 'ASC']
        );
    }
}