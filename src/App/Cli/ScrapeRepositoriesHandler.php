<?php

namespace Gitrub\App\Cli;

use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryGithubGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryScrapeStateGateway;
use Gitrub\Domain\Repository\UseCase\ScrapeRepositoryUseCase;

class ScrapeRepositoriesHandler {

	public function __construct(
		private RepositoryGateway $repository_gateway,
		private RepositoryGithubGateway $repository_github_gateway,
		private RepositoryScrapeStateGateway $repository_scrape_state_gateway,
	) {}

	public function scrape(?int $limit): string {
		(new ScrapeRepositoryUseCase($this->repository_gateway, $this->repository_github_gateway, $this->repository_scrape_state_gateway))
			->continueScrappingRepositories($limit ?? 50);
		return 'Repositories scrapped!';
	}
}
