<?php

namespace Gitrub\App\Web\Rest\Repository\Controller\Presenter;

use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Response\Response;
use Gitrub\Domain\Repository\Exception\RepositoryNotFound;

class RepositoryNotFoundPresenter implements AsResponse {

	public function __construct(
		private RepositoryNotFound $repository_not_found
	) {}

	public function asResponse(): Response {
		return new Response(
			httpCode: 404,
			body: json_encode(['error' => $this->repository_not_found->getMessage()]),
		);
	}
}
