<?php

namespace App\Controller\Admin;

use App\Entity\Permission;
use App\Entity\Profile;
use App\Form\PermissionType;
use App\Repository\PermissionRepository;
use App\Repository\ProfileRepository;
use App\Service\HelperService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/permission')]
final class PermissionController extends AbstractController
{
    #[Route(name: 'app_permission_index', methods: ['GET'])]
    public function index(PermissionRepository $permissionRepository): Response
    {
        $permissionMatrix = [];
        foreach ($permissionRepository->findAll() as $permission) {
            $profile = $permission->getProfile();
            if ($profile instanceof Profile) {
                $profileName = $profile->getId().'-'.$profile->getName();
                $permissionMatrix[$permission->getBaseRoute()][$profileName] = $permission->getAccess();
            }
        }

        return $this->render('default/admin/permission/index.html.twig', [
            'permissionMatrix' => $permissionMatrix,
        ]);
    }

    #[Route('/update', name: 'app_permission_update', methods: ['POST'])]
    public function updatePermission(Request $request, PermissionRepository $permissionRepository, EntityManagerInterface $entityManager, ProfileRepository $profileRepository, HelperService $helperService): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if (isset($data['route'], $data['profileId'], $data['isEnabled'])) {
            $profile = $profileRepository->find($data['profileId']);
            if (!$profile instanceof Profile) {
                return new JsonResponse(['status' => 'error'], 400);
            }

            $route = $data['route'];
            $baseRoute = $helperService->getBaseRoute($route);

            $permission = $permissionRepository->findOneBy([
                'baseRoute' => $baseRoute,
                'profile' => $profile,
            ]);
            if ($permission) {
                $access = $permission->getAccess();
                $access[$data['route']] = $data['isEnabled'];
                $permission->setAccess($access);
                $entityManager->flush();

                return new JsonResponse(['status' => 'success']);
            }
        }

        return new JsonResponse(['status' => 'error'], 400);
    }
}
