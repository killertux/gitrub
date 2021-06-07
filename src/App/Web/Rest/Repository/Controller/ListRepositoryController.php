<?php

namespace Gitrub\App\Web\Rest\Repository\Controller;

use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Rest\FromLimitFromQuery;
use Gitrub\App\Web\Rest\Repository\Controller\Presenter\RepositoryCollectionPresenter;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\Repository\UseCase\ListRepositoryUseCase;

class ListRepositoryController {

	public function __construct(
		private RepositoryGateway $repository_gateway
	) {}

	public function listRepositories(): AsResponse {
		return new RepositoryCollectionPresenter(
            (new ListRepositoryUseCase($this->repository_gateway))
                ->listRepositories($this->createFromLimit())
        );
	}

	public function listForkRepositories(): AsResponse {
		return new RepositoryCollectionPresenter(
            (new ListRepositoryUseCase($this->repository_gateway))
                ->listForkRepositories($this->createFromLimit())
        );
	}

	public function listRepositoriesFromOwner(int $owner_id): AsResponse {
		return new RepositoryCollectionPresenter(
            (new ListRepositoryUseCase($this->repository_gateway))
                ->listRepositoriesFromOwner($owner_id, $this->createFromLimit())
        );
	}

	public function listRepositoriesWithName(string $name): AsResponse {
		return new RepositoryCollectionPresenter(
            (new ListRepositoryUseCase($this->repository_gateway))
                ->listRepositoriesWithName($name, $this->createFromLimit())
        );
	}

	private function createFromLimit(): FromLimit {
		return (new FromLimitFromQuery(
			default_from: 0,
			default_limit: 50
		))->fromLimit();
	}
}
