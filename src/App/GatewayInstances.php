<?php

namespace Gitrub\App;

use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryGithubGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryScrapeStateGateway;
use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Domain\User\Gateway\UserGithubGateway;
use Gitrub\Domain\User\Gateway\UserScrapeStateGateway;
use Gitrub\Gateway\Database\Connection\DatabaseConnectionStringParser;
use Gitrub\Gateway\Database\Connection\PdoFromDatabaseConnectionData;
use Gitrub\Gateway\Database\Repository\DatabaseRepositoryGateway;
use Gitrub\Gateway\Database\ScrapeState\DatabaseScrapeState;
use Gitrub\Gateway\Database\User\DatabaseUserGateway;
use Gitrub\Gateway\Github\RestApi\GithubPersonalAccessToken;
use Gitrub\Gateway\Github\RestApi\Repository\RestAPIRepositoryGithubGateway;
use Gitrub\Gateway\Github\RestApi\User\RestAPIUserGithubGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryScrapeStateGateway;
use Test\Gitrub\Gateway\User\MockUserScrapeStateGateway;

class GatewayInstances {

	public function __construct(
		public UserGateway $user_gateway,
		public UserGithubGateway $user_github_gateway,
		public UserScrapeStateGateway $user_scrape_state_gateway,
		public RepositoryGateway $repository_gateway,
		public RepositoryGithubGateway $repository_github_gateway,
		public RepositoryScrapeStateGateway $repository_scrape_state_gateway,
	) {}

	public static function default(): self {
		$connection = (new PdoFromDatabaseConnectionData((new DatabaseConnectionStringParser(getenv('DATABASE_URI')))->parse()))
			->connect();
		$repository_github_gateway = new RestAPIRepositoryGithubGateway(
			new \GuzzleHttp\Client(),
			new GithubPersonalAccessToken(getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN'))
		);
		$user_github_gateway = new RestAPIUserGithubGateway(
			new \GuzzleHttp\Client(),
			new GithubPersonalAccessToken(getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN'))
		);
		$user_gateway = new DatabaseUserGateway($connection);
		$repository_gateway = new DatabaseRepositoryGateway($connection, $user_gateway);
		$user_scrape_state = new DatabaseScrapeState($connection, 'users');
		$repository_scrape_state = new DatabaseScrapeState($connection, 'repositories');
		return new self(
			user_gateway: $user_gateway,
			user_github_gateway: $user_github_gateway,
			user_scrape_state_gateway: $user_scrape_state,
			repository_gateway: $repository_gateway,
			repository_github_gateway: $repository_github_gateway,
			repository_scrape_state_gateway: $repository_scrape_state,
		);
	}
}
