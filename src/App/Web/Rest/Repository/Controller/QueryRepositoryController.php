<?php

namespace Gitrub\App\Web\Rest\Repository\Controller;

use Gitrub\App\Web\Rest\Repository\Controller\Presenter\RepositoryNotFoundPresenter;
use Gitrub\App\Web\Rest\Repository\Controller\Presenter\RepositoryPresenter;
use Gitrub\App\Web\Response\AsResponse;
use Gitrub\Domain\Repository\Exception\RepositoryNotFound;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\Repository\UseCase\QueryRepositoryUseCase;

class QueryRepositoryController {

	public function __construct(
		private RepositoryGateway $repository_gateway
	) {}

	public function getRepositoryByFullName(string $full_name): AsResponse {
		return $this->executeAndHandleExceptions(
			fn() => new RepositoryPresenter(
				(new QueryRepositoryUseCase($this->repository_gateway))
					->getRepositoryByFullName($full_name)
			)
		);
	}

	public function getRepositoryById(int $id): AsResponse {
		return $this->executeAndHandleExceptions(
			fn() => new RepositoryPresenter(
				(new QueryRepositoryUseCase($this->repository_gateway))
					->getRepositoryById($id)
			)
		);
	}

	private function executeAndHandleExceptions(callable $closure): AsResponse {
		try {
			return $closure();
		} catch (RepositoryNotFound $exception) {
			return new RepositoryNotFoundPresenter($exception);
		}
	}
}
