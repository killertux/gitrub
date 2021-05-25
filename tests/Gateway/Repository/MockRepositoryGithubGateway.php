<?php

namespace Test\Gitrub\Gateway\Repository;

use EBANX\Stream\Stream;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Gitrub\Domain\Repository\Entity\Repository;
use Gitrub\Domain\Repository\Gateway\RepositoryGithubGateway;

class MockRepositoryGithubGateway implements RepositoryGithubGateway {

	/** @var Repository[]  */
	private array $repositories = [];

	public function __construct(array $repositories) {
		usort($repositories, fn (Repository $repository_a, Repository $repository_b) => $repository_a->id <=> $repository_b->id);
		$this->repositories = $repositories;
	}

	public function listRepositories(FromLimit $from_limit): RepositoryCollection {
		return new RepositoryCollection(
			Stream::of($this->repositories)
				->filter(fn(Repository $repository) => $repository->id >= $from_limit->from)
				->take($from_limit->limit)
		);
	}
}
