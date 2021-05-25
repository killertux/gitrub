<?php

namespace Gitrub\App\Web\User\Controller\Presenter;

use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Response\Response;
use Gitrub\Domain\User\Exception\UserGithubGatewayError;

class UserGithubGatewayErrorPresenter implements AsResponse {

	public function __construct(
		private UserGithubGatewayError $user_github_gateway_error
	) {}

	public function asResponse(): Response {
		return new Response(
			httpCode: 500,
			body: json_encode(['error' => $this->user_github_gateway_error->getMessage()])
		);
	}
}
