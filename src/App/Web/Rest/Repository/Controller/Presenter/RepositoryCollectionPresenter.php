<?php

namespace Gitrub\App\Web\Rest\Repository\Controller\Presenter;

use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Response\Response;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;

class RepositoryCollectionPresenter implements AsResponse {

	public function __construct(
		private RepositoryCollection $listRepositories
	) {}

	public function asResponse(): Response {
		return new Response(
			http_code: 200,
			body: json_encode(iterator_to_array($this->listRepositories))
		);
	}
}
