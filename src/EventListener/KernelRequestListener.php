<?php

namespace App\EventListener;

use App\Security\Voter\AccessVoter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final readonly class KernelRequestListener
{
    private const UNCHECK_DEV_ROUTES = [
        '_preview_error',
        'ux_live_component',
        '_wdt',
        '_profiler_home',
        '_profiler_search',
        '_profiler_search_bar',
        '_profiler_phpinfo',
        '_profiler_xdebug',
        '_profiler_font',
        '_profiler_search_results',
        '_profiler_open_file',
        '_profiler',
        '_profiler_router',
        '_profiler_exception',
        '_profiler_exception_css',
    ];

    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
        private RequestStack $requestStack,
    )
    {
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $currentRoute = $request->attributes->get('_route');
        if (\in_array($currentRoute, self::UNCHECK_DEV_ROUTES, true)) {
            return;
        }

        if ($currentRoute !== null && !$this->authorizationChecker->isGranted(AccessVoter::ACCESS_ROUTE)) {
            throw new AccessDeniedHttpException('Access Denied.');
        }
    }
}
