<?php

namespace Test\Gitrub\Domain\Repository\UseCase;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\UseCase\QueryRepositoryUseCase;
use Gitrub\Domain\Repository\UseCase\ScrapeRepositoryUseCase;
use Gitrub\Domain\User\UseCase\QueryUserUseCase;
use Gitrub\Domain\User\UseCase\ScrapeUserUseCase;
use Test\Gitrub\Gateway\Repository\MockRepositoryGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryGithubGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryScrapeStateGateway;
use Test\Gitrub\Gateway\User\MockUserGateway;
use Test\Gitrub\Gateway\User\MockUserGithubGateway;
use Test\Gitrub\Gateway\User\MockUserScrapeStateGateway;
use Test\Gitrub\GitrubTestCase;

class ScrapeRepositoryUseCaseTest extends GitrubTestCase {

	public function testNoRepositoryFromGithub_ShouldStoreNothing(): void {
		$empty_github_gateway = new MockRepositoryGithubGateway([]);
		$empty_repository_gateway = new MockRepositoryGateway([]);

		(new ScrapeRepositoryUseCase($empty_repository_gateway, $empty_github_gateway, new MockRepositoryScrapeStateGateway()))
			->continueScrappingRepositories();

		$empty_repository_gateway->assertEmpty();
	}

	public function testRepositoriesFromGithub_ShouldStoreIntoRepositoryGateway(): void {
		$github_repositories = [$this->faker->repository(), $this->faker->repository()];
		$github_gateway = new MockRepositoryGithubGateway($github_repositories);
		$repository_gateway = new MockRepositoryGateway([]);

		(new ScrapeRepositoryUseCase($repository_gateway, $github_gateway, new MockRepositoryScrapeStateGateway()))
			->continueScrappingRepositories();

		$repository_gateway->assertSavedRepositories($github_repositories);
	}

	public function testRepositoryFromGithubUsingFromId(): void {
		$github_repositories = [$this->faker->repository(), $this->faker->repository()];
		$github_gateway = new MockRepositoryGithubGateway($github_repositories);
		$repository_gateway = new MockRepositoryGateway([]);

		(new ScrapeRepositoryUseCase($repository_gateway, $github_gateway, new MockRepositoryScrapeStateGateway()))
			->scrapeRepositoriesFromLimit(new FromLimit(from: $github_repositories[0]->id + 1, limit: 50));

		$repository_gateway->assertSavedRepositories([$github_repositories[1]]);
	}

	public function testHavingRepositoryAlreadySaved(): void {
		$repository_1 = $this->faker->repository();
		$repository_2 = $this->faker->repository();
		$repository_3 = $this->faker->repository();

		$github_gateway = new MockRepositoryGithubGateway([$repository_1, $repository_2, $repository_3]);
		$repository_gateway = new MockRepositoryGateway([$repository_1]);

		(new ScrapeRepositoryUseCase($repository_gateway, $github_gateway, new MockRepositoryScrapeStateGateway($repository_1->id)))
			->continueScrappingRepositories();

		$repository_gateway->assertSavedRepositories([$repository_1, $repository_2, $repository_3]);
	}

	public function testPassingLimit(): void {
		$repository_1 = $this->faker->repository();
		$repository_2 = $this->faker->repository();
		$repository_3 = $this->faker->repository();

		$github_gateway = new MockRepositoryGithubGateway([$repository_1, $repository_2, $repository_3]);
		$repository_gateway = new MockRepositoryGateway([$repository_1]);

		(new ScrapeRepositoryUseCase($repository_gateway, $github_gateway, new MockRepositoryScrapeStateGateway($repository_1->id)))
			->continueScrappingRepositories(limit: 1);

		$repository_gateway->assertSavedRepositories([$repository_1, $repository_2]);
	}

	public function testPassingLimitLowerThanZero(): void {
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Limit must be a positive number');
		(new ScrapeRepositoryUseCase(new MockRepositoryGateway([]), new MockRepositoryGithubGateway([]), new MockRepositoryScrapeStateGateway()))
			->continueScrappingRepositories(limit: -1);
	}

	public function testRetrieveRepositoryFromGithub_ShouldBeAvailableInOtherRepositoryUseCases(): void {
		$repository = $this->faker->repository();
		$github_gateway = new MockRepositoryGithubGateway([$repository]);
		$repository_gateway = new MockRepositoryGateway([]);

		(new ScrapeRepositoryUseCase($repository_gateway, $github_gateway, new MockRepositoryScrapeStateGateway()))
			->continueScrappingRepositories();
		$retrieved_repository = (new QueryRepositoryUseCase($repository_gateway))
			->getRepositoryById($repository->id);

		self::assertEquals($repository, $retrieved_repository);
	}

	public function testRetrieveRepositoryFromGithub_ShouldBeAvailableInOtherUserUseCases(): void {
		$repository = $this->faker->repository();
		$github_gateway = new MockRepositoryGithubGateway([$repository]);
		$user_gateway = new MockUserGateway([]);
		$repository_gateway = new MockRepositoryGateway([], $user_gateway);

		(new ScrapeRepositoryUseCase($repository_gateway, $github_gateway, new MockRepositoryScrapeStateGateway()))
			->continueScrappingRepositories();
		$user = (new QueryUserUseCase($user_gateway))
			->getUserById($repository->owner->id);

		self::assertEquals($repository->owner, $user);
	}

	public function testScrapeRepository_ScrapeUserAfterShouldRespectCorrectOrder(): void {
		$user_1 = $this->faker->user();
		$user_2 = $this->faker->user();
		$repository = $this->faker->repository(owner: $user_2);

		$github_gateway = new MockRepositoryGithubGateway([$repository]);
		$user_gateway = new MockUserGateway([]);
		$repository_gateway = new MockRepositoryGateway([], $user_gateway);

		(new ScrapeRepositoryUseCase($repository_gateway, $github_gateway, new MockRepositoryScrapeStateGateway()))
			->continueScrappingRepositories();
		(new ScrapeUserUseCase($user_gateway, new MockUserGithubGateway([$user_1, $user_2]), new MockUserScrapeStateGateway()))
			->continueScrappingUsers();

		$repository_gateway->assertSavedRepositories([$repository]);
		$user_gateway->assertSavedUsers([$user_1, $user_2]);
	}

	public function testContinueScrapping_ShouldNotBeAffectedByScrappingWithFromAndLimit(): void {
		$repository_1 = $this->faker->repository();
		$repository_2 = $this->faker->repository();
		$repsoitory_3 = $this->faker->repository();

		$github_gateway = new MockRepositoryGithubGateway([$repository_1, $repository_2, $repsoitory_3]);
		$repository_gateway = new MockRepositoryGateway([]);

		(new ScrapeRepositoryUseCase($repository_gateway, $github_gateway, new MockRepositoryScrapeStateGateway()))
			->scrapeRepositoriesFromLimit(new FromLimit(from: $repository_2->id, limit: 5));
		$repository_gateway->assertSavedRepositories([$repository_2, $repsoitory_3]);

		(new ScrapeRepositoryUseCase($repository_gateway, $github_gateway, new MockRepositoryScrapeStateGateway()))
			->continueScrappingRepositories();
		$repository_gateway->assertSavedRepositories([$repository_1, $repository_2, $repsoitory_3]);
	}
}
