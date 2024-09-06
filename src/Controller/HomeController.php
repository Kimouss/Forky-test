<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/admin/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('default/home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/admin/other', name: 'app_other')]
    public function other(): Response
    {
        return $this->render('default/home/other.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
