<?php

namespace Gitrub\App\Web\Rest\Repository\Controller\Presenter;

use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Response\Response;
use Gitrub\Domain\Repository\Exception\RepositoryGithubGatewayError;

class RepositoryGithubGatewayErrorPresenter implements AsResponse {


	public function __construct(
		private RepositoryGithubGatewayError $repository_github_gateway_error
	) {}

	public function asResponse(): Response {
		return new Response(
			httpCode: 500,
			body: json_encode(['error' => $this->repository_github_gateway_error->getMessage()]),
		);
	}
}
