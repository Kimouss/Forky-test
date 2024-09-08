<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'app_home_')]
class HomeController extends AbstractController
{
    #[Route('/home', name: 'index')]
    public function index(): Response
    {
        return $this->render('default/home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/other', name: 'other')]
    public function other(): Response
    {
        return $this->render('default/home/other.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
