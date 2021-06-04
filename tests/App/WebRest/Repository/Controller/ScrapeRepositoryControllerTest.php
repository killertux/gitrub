<?php

namespace Test\Gitrub\App\WebRest\Repository\Controller;

use EBANX\Stream\Stream;
use Gitrub\App\Web\Rest\Repository\Controller\ScrapeRepositoryController;
use Gitrub\App\Web\Rest\Response\Response;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Gitrub\Domain\Repository\Exception\RepositoryGithubGatewayError;
use Gitrub\Domain\Repository\Gateway\RepositoryGithubGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryGithubGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryScrapeStateGateway;
use Test\Gitrub\GitrubTestCase;
use Test\Gitrub\Support\Traits\GetGlobalCleaner;

class ScrapeRepositoryControllerTest extends GitrubTestCase {

	use GetGlobalCleaner;

	public function testScrapeRepositories_ShouldUseDefaultParams(): void {
		$repositories = $this->createsABunchOfRepositories(101);

		$this->assertDone((new ScrapeRepositoryController(
			$repository_gateway = new MockRepositoryGateway([]),
			new MockRepositoryGithubGateway($repositories),
			new MockRepositoryScrapeStateGateway(),
		))->scrapeRepositories()->asResponse());
		$repository_gateway->assertSavedRepositories(
			Stream::of($repositories)->take(100)->collect()
		);
	}

	public function testScrapeRepositoriesPassingLimitMultipleTimes(): void {
		$repositories = $this->createsABunchOfRepositories(11);

		$_GET['limit'] = 5;
		$controller = (new ScrapeRepositoryController(
			$user_gateway = new MockRepositoryGateway([]),
			new MockRepositoryGithubGateway($repositories),
			new MockRepositoryScrapeStateGateway(),
		));

		$this->assertDone($controller->scrapeRepositories()->asResponse());
		$this->assertDone($controller->scrapeRepositories()->asResponse());
		$user_gateway->assertSavedRepositories(
			Stream::of($repositories)
				->take(10)
				->collect()
		);
	}

	public function testScrapeRepositoriesPassingFrom(): void {
		$repositories = $this->createsABunchOfRepositories(6);

		$_GET['from'] = $repositories[3]->id;

		$this->assertDone((new ScrapeRepositoryController(
			$user_gateway = new MockRepositoryGateway([]),
			new MockRepositoryGithubGateway($repositories),
			new MockRepositoryScrapeStateGateway(),
		))->scrapeRepositories()->asResponse());
		$user_gateway->assertSavedRepositories(
			Stream::of($repositories)->skip(3)->collect()
		);
	}

	public function testPassingNegativeLimit(): void {
		$_GET['limit'] = -1;
		$response = (new ScrapeRepositoryController(
			new MockRepositoryGateway([]),
			new MockRepositoryGithubGateway([]),
			new MockRepositoryScrapeStateGateway(),
		))->scrapeRepositories()->asResponse();
		self::assertEquals(400, $response->httpCode);
		self::assertEquals('{"error":"Limit must be a positive number"}', $response->body);
	}

	public function testErrorInGithub(): void {
		$response = (new ScrapeRepositoryController(
			new MockRepositoryGateway([]),
			$this->createFailingGithubGateway(),
			new MockRepositoryScrapeStateGateway(),
		))->scrapeRepositories()->asResponse();
		self::assertEquals(500, $response->httpCode);
		self::assertEquals('{"error":"Internal github error"}', $response->body);
	}

	private function createsABunchOfRepositories(int $n_repositories): array {
		return Stream::rangeInt(1, $n_repositories)
			->map(fn($_) => $this->faker->repository())
			->collect();
	}

	private function assertDone(Response $response): void {
		self::assertEquals(new Response(200, '{"message":"done"}'), $response);
	}

	private function createFailingGithubGateway(): RepositoryGithubGateway {
		return new class implements RepositoryGithubGateway {
			public function listRepositories(FromLimit $from_limit): RepositoryCollection {
				throw new RepositoryGithubGatewayError('Internal github error');
			}
		};
	}
}
