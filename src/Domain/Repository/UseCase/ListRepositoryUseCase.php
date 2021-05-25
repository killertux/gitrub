<?php

namespace Gitrub\Domain\Repository\UseCase;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;

class ListRepositoryUseCase {

	public function __construct(
		private RepositoryGateway $repository_gateway,
	) {}

	public function listRepositories(FromLimit $from_limit_max_500): RepositoryCollection {
		$this->validateLimitNotOver500($from_limit_max_500);
		return $this->repository_gateway->listRepositories(
			$from_limit_max_500
		);
	}

	public function listForkRepositories(FromLimit $from_limit_max_500): RepositoryCollection {
		$this->validateLimitNotOver500($from_limit_max_500);
		return $this->repository_gateway->listForkRepositories(
			$from_limit_max_500
		);
	}

	public function listRepositoriesFromOwner(int $owner_id, FromLimit $from_limit_max_500): RepositoryCollection {
		$this->validateLimitNotOver500($from_limit_max_500);
		return $this->repository_gateway->listRepositoriesFromOwner(
			owner_id: $owner_id,
			from_limit: $from_limit_max_500
		);
	}

	public function listRepositoriesWithName(string $name, FromLimit $from_limit_max_500): RepositoryCollection {
		$this->validateLimitNotOver500($from_limit_max_500);
		return $this->repository_gateway->listRepositoriesWithName(
			name: $name,
			from_limit: $from_limit_max_500
		);
	}

	private function validateLimitNotOver500(FromLimit $from_limit_max_500): void {
		$from_limit_max_500->validateLimit(max_expected_limit: 500);
	}
}
