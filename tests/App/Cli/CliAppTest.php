<?php

namespace Test\Gitrub\App\Cli;

use Gitrub\App\Cli\CliApp;
use Test\Gitrub\GitrubTestCase;

class CliAppTest extends GitrubTestCase {

	public function testScrapeUsers(): void {
		$user = $this->faker->user();
		$gateway_instances = $this->faker->gatewayInstances(
			github_users: [$user]
		);

		$cli_app = new CliApp($gateway_instances);
		$cli_app->run(['scrapeUsers', '20']);

		self::assertEquals($user, $gateway_instances->user_gateway->getUserById($user->id));
	}

	public function testScrapeRepositories(): void {
		$repository = $this->faker->repository();
		$gateway_instances = $this->faker->gatewayInstances(
			github_repositories: [$repository]
		);

		$cli_app = new CliApp($gateway_instances);
		$cli_app->run(['scrapeRepositories', '20']);

		self::assertEquals($repository, $gateway_instances->repository_gateway->getRepositoryById($repository->id));
	}
}
