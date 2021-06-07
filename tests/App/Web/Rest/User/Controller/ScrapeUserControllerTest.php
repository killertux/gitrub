<?php

namespace Test\Gitrub\App\Web\Rest\User\Controller;

use EBANX\Stream\Stream;
use Gitrub\App\Web\Response\Response;
use Gitrub\App\Web\Rest\User\Controller\ScrapeUserController;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Exception\UserGithubGatewayError;
use Gitrub\Domain\User\Gateway\UserGithubGateway;
use Test\Gitrub\Gateway\User\MockUserGateway;
use Test\Gitrub\Gateway\User\MockUserGithubGateway;
use Test\Gitrub\Gateway\User\MockUserScrapeStateGateway;
use Test\Gitrub\GitrubTestCase;
use Test\Gitrub\Support\Traits\GetGlobalCleaner;

class ScrapeUserControllerTest extends GitrubTestCase {

	use GetGlobalCleaner;

	public function testScrapeUsers_ShouldUseDefaultParams(): void {
		$users = $this->createsABunchOfUsers(101);

		$this->assertDone((new ScrapeUserController(
			$user_gateway = new MockUserGateway([]),
			new MockUserGithubGateway($users),
			new MockUserScrapeStateGateway(),
		))->scrapeUsers()->asResponse());
		$user_gateway->assertSavedUsers(
			Stream::of($users)->take(100)->collect()
		);
	}

	public function testScrapeUsersPassingLimitMultipleTimes(): void {
		$users = $this->createsABunchOfUsers(41);

		$_GET['limit'] = 20;
		$controller = (new ScrapeUserController(
			$user_gateway = new MockUserGateway([]),
			new MockUserGithubGateway($users),
			new MockUserScrapeStateGateway(),
		));

		$this->assertDone($controller->scrapeUsers()->asResponse());
		$this->assertDone($controller->scrapeUsers()->asResponse());
		$user_gateway->assertSavedUsers(
			Stream::of($users)
				->take(40)
				->collect()
		);
	}

	public function testScrapeUsersPassingFrom(): void {
		$users = $this->createsABunchOfUsers(10);

		$_GET['from'] = $users[5]->id;

		$this->assertDone((new ScrapeUserController(
			$user_gateway = new MockUserGateway([]),
			new MockUserGithubGateway($users),
			new MockUserScrapeStateGateway(),
		))->scrapeUsers()->asResponse());
		$user_gateway->assertSavedUsers(
			Stream::of($users)->skip(5)->collect()
		);
	}

	private function createsABunchOfUsers(int $n_users): array {
		return Stream::rangeInt(1, $n_users)
			->map(fn($_) => $this->faker->user())
			->collect();
	}

	private function assertDone(Response $response): void {
		self::assertEquals(new Response(200, '{"message":"done"}'), $response);
	}
}