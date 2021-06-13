<?php

namespace Gitrub\App\Web\Rest\Repository\Controller;

use Gitrub\App\Web\Request\Request;
use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Rest\Repository\Controller\Presenter\RepositoryPresenter;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\Repository\UseCase\QueryRepositoryUseCase;

class QueryRepositoryController {

	public function __construct(
		private RepositoryGateway $repository_gateway
	) {}

	public function getRepositoryByFullName(Request $_, string $full_name): AsResponse {
		return new RepositoryPresenter(
            (new QueryRepositoryUseCase($this->repository_gateway))
                ->getRepositoryByFullName($full_name)
        );
	}

	public function getRepositoryById(Request $_,int $id): AsResponse {
		return new RepositoryPresenter(
            (new QueryRepositoryUseCase($this->repository_gateway))
                ->getRepositoryById($id)
        );
	}
}
