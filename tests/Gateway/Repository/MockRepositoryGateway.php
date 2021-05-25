<?php

namespace Test\Gitrub\Gateway\Repository;

use EBANX\Stream\Stream;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Gitrub\Domain\Repository\Entity\Repository;
use Gitrub\Domain\Repository\Exception\RepositoryNotFound;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Gateway\UserGateway;
use PHPUnit\Framework\Assert;
use Test\Gitrub\Gateway\User\MockUserGateway;

class MockRepositoryGateway implements RepositoryGateway {

	/** @var Repository[] */
	private array $repositories = [];
	private ?UserGateway $user_gateway;

	public function __construct(array $repositories, UserGateway $user_gateway = null) {
		$this->user_gateway = $user_gateway ?? new MockUserGateway([]);
		$this->storeRepositories(new RepositoryCollection(new \ArrayIterator($repositories)));
	}

	public function listRepositories(FromLimit $from_limit): RepositoryCollection {
		return new RepositoryCollection(
			Stream::of($this->repositories)
				->filter(fn(Repository $repository) => $repository->id >= $from_limit->from)
				->take($from_limit->limit)
		);
	}

	public function listForkRepositories(FromLimit $from_limit): RepositoryCollection {
		return new RepositoryCollection(
			Stream::of($this->repositories)
				->filter(fn(Repository $repository) => $repository->id >= $from_limit->from)
				->filter(fn(Repository $repository) => $repository->fork === true)
				->take($from_limit->limit)
		);
	}

	public function listRepositoriesFromOwner(int $owner_id, FromLimit $from_limit): RepositoryCollection {
		return new RepositoryCollection(
			Stream::of($this->repositories)
				->filter(fn(Repository $repository) => $repository->id >= $from_limit->from)
				->filter(fn(Repository $repository) => $repository->owner->id === $owner_id)
				->take($from_limit->limit)
		);
	}

	public function listRepositoriesWithName(string $name, FromLimit $from_limit): RepositoryCollection {
		return new RepositoryCollection(
			Stream::of($this->repositories)
				->filter(fn(Repository $repository) => $repository->id >= $from_limit->from)
				->filter(fn(Repository $repository) => $repository->name === $name)
				->take($from_limit->limit)
		);
	}

	public function getRepositoryByFullName(string $full_name): Repository {
		return Stream::of($this->repositories)
			->filter(fn(Repository $repository) => $repository->full_name == $full_name)
			->take(1)
			->collect()[0] ?? throw new RepositoryNotFound('Repository not found with full name ' . $full_name);
	}

	public function getRepositoryById(int $id): Repository {
		return Stream::of($this->repositories)
				->filter(fn(Repository $repository) => $repository->id == $id)
				->take(1)
				->collect()[0] ?? throw new RepositoryNotFound('Repository not found with id ' . $id);
	}

	public function storeRepositories(RepositoryCollection $repositories): void {
		$repositories = array_merge($this->repositories, iterator_to_array($repositories));
		usort($repositories, fn (Repository $repository_a, Repository $repository_b) => $repository_a->id <=> $repository_b->id);
		$this->repositories = self::unique($repositories);
		$this->user_gateway->storeUsers(
			new UserCollection(
				Stream::of($this->repositories)
					->pluck('owner')
			)
		);
	}

	public function assertEmpty(): void {
		Assert::assertEmpty($this->repositories);
	}

	public function assertSavedRepositories(array $repositories): void {
		Assert::assertEquals($this->repositories, $repositories);
	}

	private static function unique(array $repositories): array {
		$returner = [];
		foreach ($repositories as $repository) {
			$returner[$repository->id] = $repository;
		}
		return array_values($returner);
	}
}
