<?php

namespace Gitrub\App\Web\Rest\Repository\Controller\Presenter;

use Gitrub\App\Web\Rest\Response\AsResponse;
use Gitrub\App\Web\Rest\Response\Response;
use Gitrub\Domain\Repository\Entity\Repository;

class RepositoryPresenter implements AsResponse {

	public function __construct(
		private Repository $repository
	) {}

	public function asResponse(): Response {
		return new Response(
			httpCode: 200,
			body: json_encode($this->repository),
		);
	}

	public function scrapeRepositories(): AsResponse {

	}
}