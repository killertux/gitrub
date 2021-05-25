<?php

namespace Test\Gitrub\Domain\Repository\UseCase;

use Gitrub\Domain\Repository\Exception\RepositoryNotFound;
use Gitrub\Domain\Repository\UseCase\QueryRepositoryUseCase;
use Test\Gitrub\Gateway\Repository\MockRepositoryGateway;
use Test\Gitrub\GitrubTestCase;

class QueryRepositoryUseCaseTest extends GitrubTestCase {

	public function testGetRepositoryByFullName(): void {
		$repositories = [
			$repo_1 = $this->faker->repository(),
			$repo_2 = $this->faker->repository(),
		];

		$use_case = new QueryRepositoryUseCase(new MockRepositoryGateway($repositories));

		self::assertEquals($repo_1, $use_case->getRepositoryByFullName($repo_1->full_name));
		self::assertEquals($repo_2, $use_case->getRepositoryByFullName($repo_2->full_name));
	}

	public function testGetRepositoryByNameNotFound_ShouldThrowException(): void {
		$this->expectException(RepositoryNotFound::class);
		$this->expectExceptionMessage('Repository not found with full name invalid/name');
		(new QueryRepositoryUseCase(new MockRepositoryGateway([])))
			->getRepositoryByFullName('invalid/name');
	}

	public function testGetRepositoryById(): void {
		$repositories = [
			$repo_1 = $this->faker->repository(),
			$repo_2 = $this->faker->repository(),
		];

		$use_case = new QueryRepositoryUseCase(new MockRepositoryGateway($repositories));

		self::assertEquals($repo_1, $use_case->getRepositoryById($repo_1->id));
		self::assertEquals($repo_2, $use_case->getRepositoryById($repo_2->id));
	}

	public function testGetRepositoryByIdNotFound_ShouldThrowException(): void {
		$this->expectException(RepositoryNotFound::class);
		$this->expectExceptionMessage('Repository not found with id -1');
		(new QueryRepositoryUseCase(new MockRepositoryGateway([])))
			->getRepositoryById(-1);
	}
}
