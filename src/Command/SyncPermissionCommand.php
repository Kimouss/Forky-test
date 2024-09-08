<?php

namespace App\Command;

use App\Entity\Permission;
use App\Entity\Profile;
use App\Repository\PermissionRepository;
use App\Repository\ProfileRepository;
use App\Service\HelperService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

#[AsCommand(
    name: 'app:sync:permission',
    description: 'Sync permissions for profiles based on routes',
)]
class SyncPermissionCommand extends Command
{
    public function __construct(
        private readonly ProfileRepository $profileRepository,
        private readonly PermissionRepository $permissionRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface $router,
        private readonly HelperService $helperService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        // Configure the command, no arguments or options needed for now.
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Fetch all profiles
        $profiles = $this->profileRepository->findAll();
        if (!$profiles) {
            $io->error('No profiles found.');
            return Command::FAILURE;
        }

        // Get all routes
        $routeCollection = $this->router->getRouteCollection();
        $filteredRoutes = $this->filterRoutes($routeCollection);

        // Loop through profiles and create permissions
        foreach ($profiles as $profile) {
            if (\in_array($profile->getName(), Profile::UNCHECK_PROFILE, true)) {
                continue;
            }
            foreach ($filteredRoutes as $baseRoute => $routes) {
                $permission = $this->permissionRepository->findOneBy(['profile' => $profile, 'baseRoute' => $baseRoute]);
                if (!$permission instanceof Permission) {
                    $permission = new Permission();
                }
                $permission->setProfile($profile);
                $permission->setBaseRoute($baseRoute);
                $permission->setAccess(array_fill_keys($routes, false));

                $this->entityManager->persist($permission);
            }
        }

        $this->entityManager->flush();

        $io->success('Permissions synced successfully.');

        return Command::SUCCESS;
    }

    private function filterRoutes(RouteCollection $routeCollection): array
    {
        $filteredRoutes = [];

        /** @var Route $route */
        foreach ($routeCollection as $name => $route) {
            // Skip routes in the Admin directory
            $controller = $route->getDefault('_controller');
            $names = explode('_', $name);
            if (strpos($controller, 'App\\Controller\\Admin') !== false || 'app' !== $names[0]) {
                continue;
            }
            if (\in_array($name, Permission::UNCHECK_ROUTES, true)) {
                continue;
            }

            $baseRoute = $this->helperService->getBaseRoute($name);
            if (!isset($filteredRoutes[$baseRoute])) {
                $filteredRoutes[$baseRoute] = [];
            }
            $filteredRoutes[$baseRoute][] = $name;
        }

        return $filteredRoutes;
    }
}
