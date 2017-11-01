<?php

namespace Ipunkt\Laravel\OAuthIntrospection\Providers;

use Illuminate\Support\AggregateServiceProvider;

class OAuthIntrospectionServiceProvider extends AggregateServiceProvider
{
	protected $providers = [
		RouteProvider::class,
	];
}