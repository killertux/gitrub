<?php

namespace Gitrub\App\Web\Rest\Repository\Controller;

use Gitrub\App\Web\Request\Request;
use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Rest\FromLimitFromRequest;
use Gitrub\App\Web\Rest\Repository\Controller\Presenter\RepositoryCollectionPresenter;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\Repository\UseCase\ListRepositoryUseCase;

class ListRepositoryController {

	public function __construct(
		private RepositoryGateway $repository_gateway,
	) {}

	public function listRepositories(Request $request): AsResponse {
		return new RepositoryCollectionPresenter(
            (new ListRepositoryUseCase($this->repository_gateway))
                ->listRepositories($this->createFromLimit($request))
        );
	}

	public function listForkRepositories(Request $request): AsResponse {
		return new RepositoryCollectionPresenter(
            (new ListRepositoryUseCase($this->repository_gateway))
                ->listForkRepositories($this->createFromLimit($request))
        );
	}

	public function listRepositoriesFromOwner(Request $request, int $owner_id): AsResponse {
		return new RepositoryCollectionPresenter(
            (new ListRepositoryUseCase($this->repository_gateway))
                ->listRepositoriesFromOwner($owner_id, $this->createFromLimit($request))
        );
	}

	public function listRepositoriesWithName(Request $request, string $name): AsResponse {
		return new RepositoryCollectionPresenter(
            (new ListRepositoryUseCase($this->repository_gateway))
                ->listRepositoriesWithName($name, $this->createFromLimit($request))
        );
	}

	private function createFromLimit(Request $request): FromLimit {
		return (new FromLimitFromRequest(
			default_from: 0,
			default_limit: 50
		))->fromLimit($request);
	}
}
