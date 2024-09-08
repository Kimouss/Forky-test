<?php

namespace App\Security\Voter;

use App\Entity\Permission;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\PermissionRepository;
use App\Service\HelperService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class AccessVoter extends Voter
{
    public const ACCESS_ROUTE = 'ACCESS_ROUTE';

    public function __construct(
        private readonly PermissionRepository $permissionRepository,
        private readonly RequestStack $requestStack,
        private readonly HelperService $helperService,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::ACCESS_ROUTE;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        $currentRoute = $request->attributes->get('_route');
        if (\in_array($currentRoute, Permission::UNCHECK_ROUTES, true)) {
            return true;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $profile = $user->getProfile();
        if ($profile && \in_array($profile->getName(), Profile::UNCHECK_PROFILE, true)) {
            return true;
        }

        $permission = $this->permissionRepository->findOneBy([
            'profile' => $profile,
            'baseRoute' => $this->helperService->getBaseRoute($currentRoute),
        ]);

        if ($permission) {
            return $permission->getAccess()[$currentRoute] ?? false;
        }

        return false;
    }
}
