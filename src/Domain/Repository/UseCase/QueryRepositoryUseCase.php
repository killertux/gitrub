<?php

namespace Gitrub\Domain\Repository\UseCase;

use Gitrub\Domain\Repository\Entity\Repository;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;

class QueryRepositoryUseCase {

	public function __construct(
		private RepositoryGateway $repository_gateway
	) {}

	public function getRepositoryByFullName(string $full_name): Repository {
		return $this->repository_gateway->getRepositoryByFullName($full_name);
	}

	public function getRepositoryById(int $id): Repository {
		return $this->repository_gateway->getRepositoryById($id);
	}
}
