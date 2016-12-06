<?php

namespace Ipunkt\Laravel\OAuthIntrospection;

use Ipunkt\Laravel\PackageManager\PackageServiceProvider;
use Ipunkt\Laravel\PackageManager\Support\DefinesRoutes;

class OAuthIntrospectionServiceProvider extends PackageServiceProvider implements DefinesRoutes
{
    /**
     * returns routes.php file (absolute path)
     *
     * @return string
     */
    public function routesFile()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'web.php';
    }

    /**
     * returns namespace of package
     *
     * @return string
     */
    protected function namespace()
    {
        return 'oauth-introspection';
    }
}