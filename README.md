# OAuth 2.0 Token Introspection

[![Total Downloads](https://poser.pugx.org/ipunkt/laravel-oauth-introspection/d/total.svg)](https://packagist.org/packages/ipunkt/laravel-oauth-introspection)
[![Latest Stable Version](https://poser.pugx.org/ipunkt/laravel-oauth-introspection/v/stable.svg)](https://packagist.org/packages/ipunkt/laravel-oauth-introspection)
[![Latest Unstable Version](https://poser.pugx.org/ipunkt/laravel-oauth-introspection/v/unstable.svg)](https://packagist.org/packages/ipunkt/laravel-oauth-introspection)
[![License](https://poser.pugx.org/ipunkt/laravel-oauth-introspection/license.svg)](https://packagist.org/packages/ipunkt/laravel-oauth-introspection)

## Introduction

OAuth 2.0 Introspection extends Laravel Passport to separate the authorization server and the resource server.

To verify an access token at the resource server the client sends it as bearer token to the resource server and the resource server makes an introspection server-to-server call to verify data and signature of the given token.

## Installation

Just install the package on your authorization server

	composer require ipunkt/laravel-oauth-introspection

and add the Service Provider in your `config/app.php`

	\Ipunkt\Laravel\OAuthIntrospection\Providers\OAuthIntrospectionServiceProvider::class,

## Official Documentation

Documentation for OAuth 2.0 Token Introspection can be found on the [RFC 7662](https://tools.ietf.org/html/rfc7662).

## License

OAuth 2.0 Token Introspection is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
