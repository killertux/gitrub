<?php

namespace Test\Gitrub\Domain\Repository\UseCase;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\UseCase\ListRepositoryUseCase;
use Test\Gitrub\Gateway\Repository\MockRepositoryGateway;
use Test\Gitrub\GitrubTestCase;

class ListRepositoryUseCaseTest extends GitrubTestCase {

	public function testListRepositoriesWithEmptyGateway_ShouldReturnEmptyList(): void {
		$empty_gateway = new MockRepositoryGateway([]);
		$repositories = (new ListRepositoryUseCase($empty_gateway))->listRepositories(FromLimit::default());
		self::assertEmpty(iterator_to_array($repositories));
	}

	public function testListRepositories_ShouldReturnUsersFromGateway(): void {
		$mocked_repositories = [$this->faker->repository(), $this->faker->repository(),];
		$mock_gateway = new MockRepositoryGateway($mocked_repositories);
		$repositories = (new ListRepositoryUseCase($mock_gateway))->listRepositories(FromLimit::default());
		self::assertEquals($mocked_repositories, iterator_to_array($repositories));
	}

	public function testListRepositoriesAfterAnId(): void {
		$mocked_repositories = [$this->faker->repository(), $this->faker->repository(),];
		$mock_gateway = new MockRepositoryGateway($mocked_repositories);
		$repositories = (new ListRepositoryUseCase($mock_gateway))
			->listRepositories(new FromLimit(from: $mocked_repositories[0]->id + 1, limit: 50));
		self::assertEquals([$mocked_repositories[1]], iterator_to_array($repositories));
	}

	public function testListRepositoriesWithALimit(): void {
		$mocked_repositories = [$this->faker->repository(), $this->faker->repository(),];
		$mock_gateway = new MockRepositoryGateway($mocked_repositories);
		$repositories = (new ListRepositoryUseCase($mock_gateway))
			->listRepositories(new FromLimit(from: $mocked_repositories[0]->id, limit: 1));
		self::assertEquals([$mocked_repositories[0]], iterator_to_array($repositories));
	}

	public function testListForkRepositories_ShouldOnlyListRepositoriesThatAreFork(): void {
		$repositories = [
			$_non_fork_repository_1 = $this->faker->repository(),
			$fork_repository_1 = $this->faker->repository(is_fork:true),
			$_non_fork_repository_2 = $this->faker->repository(),
			$fork_repository_2 = $this->faker->repository(is_fork:true),
		];
		$mock_gateway = new MockRepositoryGateway($repositories);
		$repositories = (new ListRepositoryUseCase($mock_gateway))->listForkRepositories(FromLimit::default());
		self::assertEquals([$fork_repository_1, $fork_repository_2], iterator_to_array($repositories));
	}

	public function testListForkRepositoriesPassingAFromId_ShouldOnlyListRepositoriesThatAreForkAfterThatId(): void {
		$repositories = [
			$_non_fork_repository_1 = $this->faker->repository(),
			$fork_repository_1 = $this->faker->repository(is_fork:true),
			$_non_fork_repository_2 = $this->faker->repository(),
			$fork_repository_2 = $this->faker->repository(is_fork:true),
		];
		$mock_gateway = new MockRepositoryGateway($repositories);
		$repositories = (new ListRepositoryUseCase($mock_gateway))
			->listForkRepositories(new FromLimit(from: $fork_repository_1->id + 1, limit: 50));
		self::assertEquals([$fork_repository_2], iterator_to_array($repositories));
	}

	public function testListForkRepositoriesWithALimit_ShouldOnlyListRepositoriesUpToThatLimit(): void {
		$repositories = [
			$_non_fork_repository_1 = $this->faker->repository(),
			$fork_repository_1 = $this->faker->repository(is_fork: true),
			$_non_fork_repository_2 = $this->faker->repository(),
			$_fork_repository_2 = $this->faker->repository(is_fork:true),
		];
		$mock_gateway = new MockRepositoryGateway($repositories);
		$repositories = (new ListRepositoryUseCase($mock_gateway))
			->listForkRepositories(new FromLimit(from: $fork_repository_1->id, limit: 1));
		self::assertEquals([$fork_repository_1], iterator_to_array($repositories));
	}

	public function testListRepositoriesFromOwner_ShouldOnlyListRepositoriesFromCorrectOwner(): void {
		$owner_1 = $this->faker->user();
		$owner_2 = $this->faker->user();

		$repository_owner_1_1 = $this->faker->repository(owner: $owner_1);
		$repository_owner_2_1 = $this->faker->repository(owner: $owner_2);
		$repository_owner_1_2 = $this->faker->repository(owner: $owner_1);
		$repository_owner_2_2 = $this->faker->repository(owner: $owner_2);

		$mock_gateway = new MockRepositoryGateway([$repository_owner_1_1, $repository_owner_1_2, $repository_owner_2_1, $repository_owner_2_2]);
		$repositories_owner_1 = (new ListRepositoryUseCase($mock_gateway))
			->listRepositoriesFromOwner(owner_id: $owner_1->id, from_limit_max_500: FromLimit::default());
		$repositories_owner_2 = (new ListRepositoryUseCase($mock_gateway))
			->listRepositoriesFromOwner(owner_id: $owner_2->id, from_limit_max_500: FromLimit::default());

		self::assertEquals([$repository_owner_1_1, $repository_owner_1_2], iterator_to_array($repositories_owner_1));
		self::assertEquals([$repository_owner_2_1, $repository_owner_2_2], iterator_to_array($repositories_owner_2));
	}

	public function testListRepositoriesFromOwnerPassingAFromId(): void {
		$owner = $this->faker->user();
		$repositories = [$this->faker->repository(owner: $owner), $this->faker->repository(owner: $owner)];

		$mock_gateway = new MockRepositoryGateway($repositories);
		$repositories_owner = (new ListRepositoryUseCase($mock_gateway))
			->listRepositoriesFromOwner(owner_id: $owner->id, from_limit_max_500: new FromLimit(from: $repositories[0]->id + 1, limit: 50));

		self::assertEquals([$repositories[1]], iterator_to_array($repositories_owner));
	}

	public function testListRepositoriesFromOwnerPassingALimit(): void {
		$owner = $this->faker->user();
		$repositories = [$this->faker->repository(owner: $owner), $this->faker->repository(owner: $owner)];

		$mock_gateway = new MockRepositoryGateway($repositories);
		$repositories_owner = (new ListRepositoryUseCase($mock_gateway))
			->listRepositoriesFromOwner(owner_id: $owner->id, from_limit_max_500: new FromLimit(from: $repositories[0]->id, limit: 1));

		self::assertEquals([$repositories[0]], iterator_to_array($repositories_owner));
	}

	public function testListRepositoriesWithName(): void {
		$repositories = [
			$repo_1_name_1 = $this->faker->repository(name: 'name_1'),
			$repo_1_name_2 = $this->faker->repository(name: 'name_2'),
			$repo_2_name_1 = $this->faker->repository(name: 'name_1'),
			$repo_2_name_2 = $this->faker->repository(name: 'name_2'),
		];

		$mock_gateway = new MockRepositoryGateway($repositories);
		$repositories_name_1 = (new ListRepositoryUseCase($mock_gateway))
			->listRepositoriesWithName(name: 'name_1', from_limit_max_500: FromLimit::default());
		$repositories_name_2 = (new ListRepositoryUseCase($mock_gateway))
			->listRepositoriesWithName(name: 'name_2', from_limit_max_500: FromLimit::default());

		self::assertEquals([$repo_1_name_1, $repo_2_name_1], iterator_to_array($repositories_name_1));
		self::assertEquals([$repo_1_name_2, $repo_2_name_2], iterator_to_array($repositories_name_2));
	}

	public function testListRepositoriesWithNamePassingFromId(): void {
		$mock_repositories = [
			$repo_1 = $this->faker->repository(name: 'name_1'),
			$repo_2 = $this->faker->repository(name: 'name_1'),
		];

		$mock_gateway = new MockRepositoryGateway($mock_repositories);
		$repositories = (new ListRepositoryUseCase($mock_gateway))
			->listRepositoriesWithName(name: 'name_1', from_limit_max_500: new FromLimit(from: $repo_1->id + 1, limit: 50));

		self::assertEquals([$repo_2], iterator_to_array($repositories));
	}

	public function testListRepositoriesWithNamePassingLimit(): void {
		$mock_repositories = [
			$repo_1 = $this->faker->repository(name: 'name_1'),
			$repo_2 = $this->faker->repository(name: 'name_1'),
		];

		$mock_gateway = new MockRepositoryGateway($mock_repositories);
		$repositories = (new ListRepositoryUseCase($mock_gateway))
			->listRepositoriesWithName(name: 'name_1', from_limit_max_500: new FromLimit(from: $repo_1->id, limit: 1));

		self::assertEquals([$repo_1], iterator_to_array($repositories));
	}
}
