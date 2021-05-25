<?php

namespace Test\Gitrub\App\Cli;

use EBANX\Stream\Stream;
use Gitrub\App\Cli\ScrapeRepositoriesHandler;
use Test\Gitrub\Gateway\Repository\MockRepositoryGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryGithubGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryScrapeStateGateway;
use Test\Gitrub\GitrubTestCase;

class ScrapeRepositoriesHandlerTest extends GitrubTestCase {

	public function testScrapeWithDefaultParams(): void {
		$repositories = $this->faker->createsABunchOfRepositories(n_repositories: 51);
		$response = (new ScrapeRepositoriesHandler($gateway = new MockRepositoryGateway([]), new MockRepositoryGithubGateway($repositories), new MockRepositoryScrapeStateGateway()))
			->scrape(null);
		self::assertEquals('Repositories scrapped!', $response);
		$gateway->assertSavedRepositories(
			Stream::of($repositories)->take(50)->collect()
		);
	}

	public function testScrape(): void {
		$repositories = $this->faker->createsABunchOfRepositories(n_repositories: 5);
		$scrape_users = new ScrapeRepositoriesHandler($gateway = new MockRepositoryGateway([]), new MockRepositoryGithubGateway($repositories), new MockRepositoryScrapeStateGateway());
		$scrape_users->scrape(limit: 2);
		$scrape_users->scrape(limit: 2);
		$gateway->assertSavedRepositories(
			Stream::of($repositories)->take(4)->collect()
		);
	}
}
