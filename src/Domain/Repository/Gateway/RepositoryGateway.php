<?php

namespace Gitrub\Domain\Repository\Gateway;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Gitrub\Domain\Repository\Entity\Repository;

interface RepositoryGateway {

	public function listRepositories(FromLimit $from_limit): RepositoryCollection;

	public function listForkRepositories(FromLimit $from_limit): RepositoryCollection;

	public function listRepositoriesFromOwner(int $owner_id, FromLimit $from_limit): RepositoryCollection;

	public function listRepositoriesWithName(string $name, FromLimit $from_limit): RepositoryCollection;

	public function getRepositoryByFullName(string $full_name): Repository;

	public function getRepositoryById(int $id): Repository;

	public function storeRepositories(RepositoryCollection $repositories): void;
}
