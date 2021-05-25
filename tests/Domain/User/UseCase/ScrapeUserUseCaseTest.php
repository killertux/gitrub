<?php

namespace Test\Gitrub\Domain\User\UseCase;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\UseCase\ScrapeUserUseCase;
use Test\Gitrub\Gateway\User\MockUserGateway;
use Test\Gitrub\Gateway\User\MockUserGithubGateway;
use Test\Gitrub\Gateway\User\MockUserScrapeStateGateway;
use Test\Gitrub\GitrubTestCase;

class ScrapeUserUseCaseTest extends GitrubTestCase {

	public function testNoUserFromGithub_ShouldStoreNothing(): void {
		$empty_github_gateway = new MockUserGithubGateway([]);
		$empty_user_gateway = new MockUserGateway([]);

		(new ScrapeUserUseCase($empty_user_gateway, $empty_github_gateway, new MockUserScrapeStateGateway()))
			->continueScrappingUsers();

		$empty_user_gateway->assertEmpty();
	}

	public function testUserFromGithub_ShouldStoreIntoUserGateway(): void {
		$github_users = [$this->faker->user(), $this->faker->user()];
		$github_gateway = new MockUserGithubGateway($github_users);
		$user_gateway = new MockUserGateway([]);

		(new ScrapeUserUseCase($user_gateway, $github_gateway, new MockUserScrapeStateGateway()))
			->continueScrappingUsers();

		$user_gateway->assertSavedUsers($github_users);
	}

	public function testUserFromGithubUsingFromId(): void {
		$github_users = [$this->faker->user(), $this->faker->user()];
		$github_gateway = new MockUserGithubGateway($github_users);
		$user_gateway = new MockUserGateway([]);

		(new ScrapeUserUseCase($user_gateway, $github_gateway, new MockUserScrapeStateGateway()))
			->scrapeUsersFromLimit(new FromLimit(from: $github_users[0]->id + 1, limit: 50));

		$user_gateway->assertSavedUsers([$github_users[1]]);
	}

	public function testHavingUsersAlreadySaved(): void {
		$user_1 = $this->faker->user();
		$user_2 = $this->faker->user();
		$user_3 = $this->faker->user();

		$github_gateway = new MockUserGithubGateway([$user_1, $user_2, $user_3]);
		$user_gateway = new MockUserGateway([$user_1]);

		(new ScrapeUserUseCase($user_gateway, $github_gateway, new MockUserScrapeStateGateway($user_1->id)))
			->continueScrappingUsers();

		$user_gateway->assertSavedUsers([$user_1, $user_2, $user_3]);
	}

	public function testPassingLimit(): void {
		$user_1 = $this->faker->user();
		$user_2 = $this->faker->user();
		$user_3 = $this->faker->user();

		$github_gateway = new MockUserGithubGateway([$user_1, $user_2, $user_3]);
		$user_gateway = new MockUserGateway([$user_1]);

		(new ScrapeUserUseCase($user_gateway, $github_gateway, new MockUserScrapeStateGateway($user_1->id)))
			->continueScrappingUsers(limit: 1);

		$user_gateway->assertSavedUsers([$user_1, $user_2]);
	}

	public function testContinueScrapping_ShouldNotBeAffectedByScrappingWithFromAndLimit(): void {
		$user_1 = $this->faker->user();
		$user_2 = $this->faker->user();
		$user_3 = $this->faker->user();

		$github_gateway = new MockUserGithubGateway([$user_1, $user_2, $user_3]);
		$user_gateway = new MockUserGateway([]);

		(new ScrapeUserUseCase($user_gateway, $github_gateway, new MockUserScrapeStateGateway()))
			->scrapeUsersFromLimit(new FromLimit(from: $user_2->id, limit: 5));
		$user_gateway->assertSavedUsers([$user_2, $user_3]);

		(new ScrapeUserUseCase($user_gateway, $github_gateway, new MockUserScrapeStateGateway()))
			->continueScrappingUsers();
		$user_gateway->assertSavedUsers([$user_1, $user_2, $user_3]);
	}

	public function testPassingLimitLowerThanZero(): void {
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Limit must be a positive number');
		(new ScrapeUserUseCase(new MockUserGateway([]), new MockUserGithubGateway([]), new MockUserScrapeStateGateway()))
			->continueScrappingUsers(limit: -1);
	}

}
