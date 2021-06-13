<?php

namespace Test\Gitrub\App\Web\Rest\Repository\Controller;

use EBANX\Stream\Stream;
use Gitrub\App\Web\Request\Request;
use Gitrub\App\Web\Response\Response;
use Gitrub\App\Web\Rest\Repository\Controller\ScrapeRepositoryController;
use Test\Gitrub\Gateway\Repository\MockRepositoryGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryGithubGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryScrapeStateGateway;
use Test\Gitrub\GitrubTestCase;

class ScrapeRepositoryControllerTest extends GitrubTestCase {

	public function testScrapeRepositories_ShouldUseDefaultParams(): void {
		$repositories = $this->createsABunchOfRepositories(101);

		$this->assertDone((new ScrapeRepositoryController(
			$repository_gateway = new MockRepositoryGateway([]),
			new MockRepositoryGithubGateway($repositories),
			new MockRepositoryScrapeStateGateway(),
		))->scrapeRepositories(Request::empty())->asResponse());
		$repository_gateway->assertSavedRepositories(
			Stream::of($repositories)->take(100)->collect()
		);
	}

	public function testScrapeRepositoriesPassingLimitMultipleTimes(): void {
		$repositories = $this->createsABunchOfRepositories(11);

		$controller = (new ScrapeRepositoryController(
			$user_gateway = new MockRepositoryGateway([]),
			new MockRepositoryGithubGateway($repositories),
			new MockRepositoryScrapeStateGateway(),
		));

		$this->assertDone($controller->scrapeRepositories(new Request(['limit' => 5]))->asResponse());
		$this->assertDone($controller->scrapeRepositories(new Request(['limit' => 5]))->asResponse());
		$user_gateway->assertSavedRepositories(
			Stream::of($repositories)
				->take(10)
				->collect()
		);
	}

	public function testScrapeRepositoriesPassingFrom(): void {
		$repositories = $this->createsABunchOfRepositories(6);

		$this->assertDone((new ScrapeRepositoryController(
			$user_gateway = new MockRepositoryGateway([]),
			new MockRepositoryGithubGateway($repositories),
			new MockRepositoryScrapeStateGateway(),
		))->scrapeRepositories(new Request(['from' => $repositories[3]->id]))->asResponse());
		$user_gateway->assertSavedRepositories(
			Stream::of($repositories)->skip(3)->collect()
		);
	}

	private function createsABunchOfRepositories(int $n_repositories): array {
		return Stream::rangeInt(1, $n_repositories)
			->map(fn($_) => $this->faker->repository())
			->collect();
	}

	private function assertDone(Response $response): void {
		self::assertEquals(new Response(200, '{"message":"done"}'), $response);
	}
}
