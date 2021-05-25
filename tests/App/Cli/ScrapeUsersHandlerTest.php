<?php

namespace Test\Gitrub\App\Cli;

use EBANX\Stream\Stream;
use Gitrub\App\Cli\ScrapeUsersHandler;
use Test\Gitrub\Gateway\User\MockUserGateway;
use Test\Gitrub\Gateway\User\MockUserGithubGateway;
use Test\Gitrub\Gateway\User\MockUserScrapeStateGateway;
use Test\Gitrub\GitrubTestCase;

class ScrapeUsersHandlerTest extends GitrubTestCase {

	public function testScrapeWithDefaultParams(): void {
		$users = $this->faker->createsABunchOfUsers(n_users: 51);
		$response = (new ScrapeUsersHandler($gateway = new MockUserGateway([]), new MockUserGithubGateway($users), new MockUserScrapeStateGateway()))
			->scrape(null);
		self::assertEquals('Users scrapped!', $response);
		$gateway->assertSavedUsers(
			Stream::of($users)->take(50)->collect()
		);
	}

	public function testScrape(): void {
		$users = $this->faker->createsABunchOfUsers(n_users: 5);
		$scrape_users = new ScrapeUsersHandler($gateway = new MockUserGateway([]), new MockUserGithubGateway($users), new MockUserScrapeStateGateway());
		$scrape_users->scrape(limit: 2);
		$scrape_users->scrape(limit: 2);
		$gateway->assertSavedUsers(
			Stream::of($users)->take(4)->collect()
		);
	}
}
