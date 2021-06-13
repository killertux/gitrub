<?php

namespace Test\Gitrub\App\Web\Rest\User\Controller;

use EBANX\Stream\Stream;
use Gitrub\App\Web\Request\Request;
use Gitrub\App\Web\Response\Response;
use Gitrub\App\Web\Rest\User\Controller\ScrapeUserController;
use Test\Gitrub\Gateway\User\MockUserGateway;
use Test\Gitrub\Gateway\User\MockUserGithubGateway;
use Test\Gitrub\Gateway\User\MockUserScrapeStateGateway;
use Test\Gitrub\GitrubTestCase;

class ScrapeUserControllerTest extends GitrubTestCase {

	public function testScrapeUsers_ShouldUseDefaultParams(): void {
		$users = $this->createsABunchOfUsers(101);

		$this->assertDone((new ScrapeUserController(
			$user_gateway = new MockUserGateway([]),
			new MockUserGithubGateway($users),
			new MockUserScrapeStateGateway(),
		))->scrapeUsers(Request::empty())->asResponse());
		$user_gateway->assertSavedUsers(
			Stream::of($users)->take(100)->collect()
		);
	}

	public function testScrapeUsersPassingLimitMultipleTimes(): void {
		$users = $this->createsABunchOfUsers(41);

		$controller = (new ScrapeUserController(
			$user_gateway = new MockUserGateway([]),
			new MockUserGithubGateway($users),
			new MockUserScrapeStateGateway(),
		));

		$this->assertDone($controller->scrapeUsers(new Request(['limit' => 20]))->asResponse());
		$this->assertDone($controller->scrapeUsers(new Request(['limit' => 20]))->asResponse());
		$user_gateway->assertSavedUsers(
			Stream::of($users)
				->take(40)
				->collect()
		);
	}

	public function testScrapeUsersPassingFrom(): void {
		$users = $this->createsABunchOfUsers(10);

		$this->assertDone((new ScrapeUserController(
			$user_gateway = new MockUserGateway([]),
			new MockUserGithubGateway($users),
			new MockUserScrapeStateGateway(),
		))->scrapeUsers(new Request(['from' => $users[5]->id]))->asResponse());
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
