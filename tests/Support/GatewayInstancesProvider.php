<?php

namespace Test\Gitrub\Support;

use Faker\Provider\Base;
use Gitrub\App\GatewayInstances;
use Test\Gitrub\Gateway\Repository\MockRepositoryGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryGithubGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryScrapeStateGateway;
use Test\Gitrub\Gateway\User\MockUserGateway;
use Test\Gitrub\Gateway\User\MockUserGithubGateway;
use Test\Gitrub\Gateway\User\MockUserScrapeStateGateway;

class GatewayInstancesProvider extends Base {

	public function gatewayInstances(
		array $users = [],
		array $github_users = [],
		array $repositories = [],
		array $github_repositories = []
	): GatewayInstances {
		return new GatewayInstances(
			user_gateway: new MockUserGateway($users),
			user_github_gateway: new MockUserGithubGateway($github_users),
			user_scrape_state_gateway: new MockUserScrapeStateGateway(),
			repository_gateway: new MockRepositoryGateway($repositories),
			repository_github_gateway: new MockRepositoryGithubGateway($github_repositories),
			repository_scrape_state_gateway: new MockRepositoryScrapeStateGateway(),
		);
	}
}
