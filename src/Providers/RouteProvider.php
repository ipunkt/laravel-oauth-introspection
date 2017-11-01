<?php

namespace Ipunkt\Laravel\OAuthIntrospection\Providers;

use Ipunkt\Laravel\PackageManager\Providers\RouteServiceProvider;

class RouteProvider extends RouteServiceProvider
{
	protected $packagePath = __DIR__ . '/../../';

	protected $routesNamespace = '\Ipunkt\Laravel\OAuthIntrospection\Http\Controllers';

	protected $routesMiddleware = null;

	protected $routesFile = 'routes/web.php';
}