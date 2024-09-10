<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractDipliController extends AbstractController
{
    protected function overrideRender(string $view, array $parameters = [], ?Response $response = null): Response
    {
        if (str_starts_with($view, 'default/')) {
            $templateFolder = getenv('TEMPLATE_FOLDER') ?: 'override';
            $modifiedPath = str_replace('default/', $templateFolder . '/', $view);
            if (file_exists(__DIR__ . '/../../templates/' . $modifiedPath)) {
                return $this->render($modifiedPath, $parameters, $response);
            }
        }

        return $this->render($view, $parameters, $response);
    }
}