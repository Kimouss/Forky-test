<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test', name: 'app_test_')]
class TestController extends AbstractDipliController
{
    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->overrideRender('default/test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
