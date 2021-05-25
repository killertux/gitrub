<?php

namespace Test\Gitrub\Gateway\Database\Repository;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Gitrub\Domain\Repository\Exception\RepositoryNotFound;
use Gitrub\Gateway\Database\Repository\DatabaseRepositoryGateway;
use Gitrub\Gateway\Database\User\DatabaseUserGateway;
use Test\Gitrub\GitrubTestCase;
use Test\Gitrub\Support\Traits\DatabaseRestore;

class DatabaseRepositoryGatewayTest extends GitrubTestCase {

	use DatabaseRestore;

	public function testStoreAndListRepositories(): void {
		$repositories = [$this->faker->repository(), $this->faker->repository(), $this->faker->repository()];

		$gateway = new DatabaseRepositoryGateway($this->pdo, new DatabaseUserGateway($this->pdo));
		$gateway->storeRepositories(new RepositoryCollection(new \ArrayIterator($repositories)));

		self::assertEquals(
			$repositories,
			iterator_to_array($gateway->listRepositories(FromLimit::default()))
		);
	}

	public function testListRepositoriesFromLimit(): void {
		$repositories = [$this->faker->repository(), $this->faker->repository(), $this->faker->repository(),  $this->faker->repository()];

		$gateway = new DatabaseRepositoryGateway($this->pdo, new DatabaseUserGateway($this->pdo));
		$gateway->storeRepositories(new RepositoryCollection(new \ArrayIterator($repositories)));

		self::assertEquals(
			[$repositories[1], $repositories[2]],
			iterator_to_array($gateway->listRepositories(new FromLimit(from: $repositories[1]->id, limit: 2)))
		);
	}

	public function testListForkRepositoriesFromLimit(): void {
		$repositories = [$this->faker->repository(is_fork: true), $this->faker->repository(), $this->faker->repository(),  $this->faker->repository(is_fork: true),  $this->faker->repository(is_fork: true)];

		$gateway = new DatabaseRepositoryGateway($this->pdo, new DatabaseUserGateway($this->pdo));
		$gateway->storeRepositories(new RepositoryCollection(new \ArrayIterator($repositories)));

		self::assertEquals(
			[$repositories[0], $repositories[3]],
			iterator_to_array($gateway->listForkRepositories(new FromLimit(from: $repositories[0]->id, limit: 2)))
		);
	}

	public function testListRepositoriesFromOwner(): void {
		$owner = $this->faker->user();
		$repositories = [
			$this->faker->repository(owner: $owner),
			$this->faker->repository(),
			$this->faker->repository(),
			$this->faker->repository(owner: $owner),
			$this->faker->repository(owner: $owner)
		];

		$gateway = new DatabaseRepositoryGateway($this->pdo, new DatabaseUserGateway($this->pdo));
		$gateway->storeRepositories(new RepositoryCollection(new \ArrayIterator($repositories)));

		self::assertEquals(
			[$repositories[0], $repositories[3]],
			iterator_to_array($gateway->listRepositoriesFromOwner($owner->id, new FromLimit(from: $repositories[0]->id, limit: 2)))
		);
	}

	public function testListRepositoriesWithName(): void {
		$repositories = [
			$this->faker->repository(name:'name'),
			$this->faker->repository(),
			$this->faker->repository(),
			$this->faker->repository(name:'name'),
			$this->faker->repository(name:'name')
		];

		$gateway = new DatabaseRepositoryGateway($this->pdo, new DatabaseUserGateway($this->pdo));
		$gateway->storeRepositories(new RepositoryCollection(new \ArrayIterator($repositories)));

		self::assertEquals(
			[$repositories[0], $repositories[3]],
			iterator_to_array($gateway->listRepositoriesWithName('name', new FromLimit(from: $repositories[0]->id, limit: 2)))
		);
	}

	public function testGetRepositoryByFullName(): void {
		$repositories = [
			$this->faker->repository(),
			$this->faker->repository(),
		];

		$gateway = new DatabaseRepositoryGateway($this->pdo, new DatabaseUserGateway($this->pdo));
		$gateway->storeRepositories(new RepositoryCollection(new \ArrayIterator($repositories)));

		self::assertEquals(
			$repositories[0],
			$gateway->getRepositoryByFullName($repositories[0]->full_name)
		);
		self::assertEquals(
			$repositories[1],
			$gateway->getRepositoryByFullName($repositories[1]->full_name)
		);
	}

	public function testGetRepositoryById(): void {
		$repositories = [
			$this->faker->repository(),
			$this->faker->repository(),
		];

		$gateway = new DatabaseRepositoryGateway($this->pdo, new DatabaseUserGateway($this->pdo));
		$gateway->storeRepositories(new RepositoryCollection(new \ArrayIterator($repositories)));

		self::assertEquals(
			$repositories[0],
			$gateway->getRepositoryById($repositories[0]->id)
		);
		self::assertEquals(
			$repositories[1],
			$gateway->getRepositoryById($repositories[1]->id)
		);
	}

	public function testRepositoryNotFoundByFullName(): void {
		$this->expectException(RepositoryNotFound::class);
		$this->expectExceptionMessage('Repository not found with full name invalid/repository');

		$gateway = new DatabaseRepositoryGateway($this->pdo, new DatabaseUserGateway($this->pdo));
		$gateway->storeRepositories(new RepositoryCollection(new \ArrayIterator([])));
		$gateway->getRepositoryByFullName('invalid/repository');
	}

	public function testRepositoryNotFoundById(): void {
		$this->expectException(RepositoryNotFound::class);
		$this->expectExceptionMessage('Repository not found with id -1');

		$gateway = new DatabaseRepositoryGateway($this->pdo, new DatabaseUserGateway($this->pdo));
		$gateway->storeRepositories(new RepositoryCollection(new \ArrayIterator([])));
		$gateway->getRepositoryById(-1);
	}

	public function test(): void {
		$repository = $this->faker->repository();
		$repository->description = null;
		$repositories = [$repository];

		$gateway = new DatabaseRepositoryGateway($this->pdo, new DatabaseUserGateway($this->pdo));
		$gateway->storeRepositories(new RepositoryCollection(new \ArrayIterator($repositories)));

		self::assertEquals(
			$repositories,
			iterator_to_array($gateway->listRepositories(FromLimit::default()))
		);
	}
}
