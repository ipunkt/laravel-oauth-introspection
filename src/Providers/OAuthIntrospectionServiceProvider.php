<?php

namespace Ipunkt\Laravel\OAuthIntrospection\Providers;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Support\Facades\Auth;
use Ipunkt\Laravel\OAuthIntrospection\Http\Controllers\IntrospectionController;

class OAuthIntrospectionServiceProvider extends AggregateServiceProvider
{
	protected $providers = [
		RouteProvider::class,
	];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->when(IntrospectionController::class)
            ->needs(UserProvider::class)
            ->give(function(){
                return Auth::createUserProvider();
            });
    }
}