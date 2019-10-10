<?php

namespace Ipunkt\Laravel\OAuthIntrospection\Http\Controllers;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response as Psr7Response;

class IntrospectionController
{
	/**
	 * @var \Lcobucci\JWT\Parser
	 */
	private $jwt;

	/**
	 * @var \League\OAuth2\Server\ResourceServer
	 */
	private $resourceServer;

	/**
	 * @var \Laravel\Passport\Bridge\AccessTokenRepository
	 */
	private $accessTokenRepository;

	/**
	 * @var \Laravel\Passport\ClientRepository
	 */
	private $clientRepository;

    /**
     * @var Illuminate\Contracts\Auth\UserProvider
     */
    private $userProvider;

    /**
     * @var string
     */
    protected $usernameProperty = 'email';

	/**
	 * constructing IntrospectionController
	 *
	 * @param \Lcobucci\JWT\Parser $jwt
	 * @param \League\OAuth2\Server\ResourceServer $resourceServer
	 * @param \Laravel\Passport\Bridge\AccessTokenRepository $accessTokenRepository
	 * @param \Laravel\Passport\ClientRepository
     * @param \Illuminate\Contracts\Auth\UserProvider $userProvider
	 */
	public function __construct(
		Parser $jwt,
		ResourceServer $resourceServer,
		AccessTokenRepository $accessTokenRepository,
		ClientRepository $clientRepository,
        UserProvider $userProvider
	)
	{
		$this->jwt = $jwt;
		$this->resourceServer = $resourceServer;
		$this->accessTokenRepository = $accessTokenRepository;
		$this->clientRepository = $clientRepository;
        $this->userProvider = $userProvider;
	}

	/**
	 * Authorize a client to access the user's account.
	 *
	 * @param  ServerRequestInterface $request
	 *
	 * @return JsonResponse|ResponseInterface
	 */
	public function introspectToken(ServerRequestInterface $request)
	{
		try {
			$this->resourceServer->validateAuthenticatedRequest($request);

			if (array_get($request->getParsedBody(), 'token_type_hint', 'access_token') !== 'access_token') {
				//  unsupported introspection
				return $this->notActiveResponse();
			}

			$accessToken = array_get($request->getParsedBody(), 'token');
			if ($accessToken === null) {
				return $this->notActiveResponse();
			}

			$token = $this->jwt->parse($accessToken);
			if (!$this->verifyToken($token)) {
				return $this->errorResponse([
					'error' => [
						'title' => 'Token invalid'
					]
				]);
			}

			# get user by token subject ID, from the UserProvider
			$user = $this->userProvider->retrieveById($token->getClaim('sub'));
            if( is_null($user) ) {
                return $this->notActiveResponse();
            }

			return $this->jsonResponse([
				'active' => true,
				'scope' => trim(implode(' ', (array)$token->getClaim('scopes', []))),
				'client_id' => intval($token->getClaim('aud')),
				'username' => $user->{$this->usernameProperty} ?? null,
				'token_type' => 'access_token',
				'exp' => intval($token->getClaim('exp')),
				'iat' => intval($token->getClaim('iat')),
				'nbf' => intval($token->getClaim('nbf')),
				'sub' => intval($token->getClaim('sub')),
				'aud' => intval($token->getClaim('aud')),
				'jti' => $token->getClaim('jti'),
			]);
		} catch (OAuthServerException $oAuthServerException) {
			return $oAuthServerException->generateHttpResponse(new Psr7Response);
		} catch (\Exception $exception) {
			return $this->exceptionResponse($exception);
		}
	}

	/**
	 * returns inactive token message
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	private function notActiveResponse() : JsonResponse
	{
		return $this->jsonResponse(['active' => false]);
	}

	/**
	 * @param array|mixed $data
	 * @param int $status
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	private function jsonResponse($data, $status = 200) : JsonResponse
	{
		return new JsonResponse($data, $status);
	}

	private function verifyToken(Token $token) : bool
	{
		$signer = new \Lcobucci\JWT\Signer\Rsa\Sha256();
		$publicKey = 'file://' . Passport::keyPath('oauth-public.key');

		try {
			if (!$token->verify($signer, $publicKey)) {
				return false;
			}

			$data = new ValidationData();
			$data->setCurrentTime(time());

			if (!$token->validate($data)) {
				return false;
			}

			//  is token revoked?
			if ($this->accessTokenRepository->isAccessTokenRevoked($token->getClaim('jti'))) {
				return false;
			}

			if ($this->clientRepository->revoked($token->getClaim('aud'))) {
				return false;
			}

			return true;
		} catch (\Exception $exception) {
		}

		return false;
	}

	/**
	 * @param array $data
	 * @param int $status
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	private function errorResponse($data, $status = 400) : JsonResponse
	{
		return $this->jsonResponse($data, $status);
	}

	/**
	 * returns an error
	 *
	 * @param \Exception $exception
	 * @param int $status
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	private function exceptionResponse(\Exception $exception, $status = 500) : JsonResponse
	{
		return $this->errorResponse([
			'error' => [
				'id' => str_slug(get_class($exception) . ' ' . $status),
				'status' => $status,
				'title' => $exception->getMessage(),
				'detail' => $exception->getTraceAsString()
			],
		], $status);
	}
}