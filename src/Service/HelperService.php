<?php

namespace App\Service;

class HelperService
{
    public function getBaseRoute(?string $routeName): ?string
    {
        if (!$routeName) {
            return $routeName;
        }
        $names = explode('_', $routeName);
        $baseRoute = '';
        for ($i = 0; $i < count($names) - 1; $i++) {
            $baseRoute .= $names[$i].'_';
        }

        return $baseRoute;
    }
}