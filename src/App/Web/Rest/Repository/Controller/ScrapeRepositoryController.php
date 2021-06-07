<?php

namespace Gitrub\App\Web\Rest\Repository\Controller;

use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Response\Response;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Exception\RepositoryGithubGatewayError;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryGithubGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryScrapeStateGateway;
use Gitrub\Domain\Repository\UseCase\ScrapeRepositoryUseCase;

class ScrapeRepositoryController {

	public function __construct(
		private RepositoryGateway $repository_gateway,
		private RepositoryGithubGateway $repository_github_gateway,
		private RepositoryScrapeStateGateway $repository_scrape_state_gateway,
	) {}

	public function scrapeRepositories(): AsResponse {
        $from = $_GET['from'] ?? null;
        $limit = $_GET['limit'] ?? 100;

        $this->executeUseCase($from ? (int)$from : null, (int)$limit);
        return new Response(
            http_code: 200,
            body: json_encode(['message' => 'done'])
        );
	}

	/** @throws RepositoryGithubGatewayError */
	private function executeUseCase(?int $from, int $limit): void {
		$use_case = new ScrapeRepositoryUseCase($this->repository_gateway, $this->repository_github_gateway, $this->repository_scrape_state_gateway);
		if ($from === null) {
			$use_case->continueScrappingRepositories($limit);
			return;
		}
		$use_case->scrapeRepositoriesFromLimit(new FromLimit($from, $limit));
	}
}
