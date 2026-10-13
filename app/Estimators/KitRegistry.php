<?php

namespace App\Estimators;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class KitRegistry
{
    /** @return list<string> */
    public static function slugs(): array
    {
        return array_keys((array) config('worksitem_estimators.tools', []));
    }

    public static function get(string $slug): KitEstimator
    {
        $class = config('worksitem_estimators.tools.'.$slug);
        if (! $class || ! class_exists($class)) {
            throw new NotFoundHttpException();
        }

        return app($class);
    }
}
