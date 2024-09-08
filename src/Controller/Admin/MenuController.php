<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use App\Service\Twig\MenuService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/menu', name: 'app_menu_')]
final class MenuController extends AbstractController
{
    #[Route(name: 'index', methods: ['GET'])]
    public function index(MenuRepository $menuRepository): Response
    {
        return $this->render('default/admin/menu/index.html.twig', [
            'menus' => $menuRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MenuService $menuService): Response
    {
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($menu);
            $entityManager->flush();
            $menuService->findAndStore();

            return $this->redirectToRoute('app_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('default/admin/menu/new.html.twig', [
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Menu $menu): Response
    {
        return $this->render('default/admin/menu/show.html.twig', [
            'menu' => $menu,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Menu $menu, EntityManagerInterface $entityManager, MenuService $menuService): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $menuService->findAndStore();

            return $this->redirectToRoute('app_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('default/admin/menu/edit.html.twig', [
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Menu $menu, EntityManagerInterface $entityManager, MenuService $menuService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$menu->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($menu);
            $entityManager->flush();
            $menuService->findAndStore();
        }

        return $this->redirectToRoute('app_menu_index', [], Response::HTTP_SEE_OTHER);
    }
}
