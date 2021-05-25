<?php

namespace Gitrub\Domain\Repository\UseCase;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\General\IteratorWithLastElementSaved;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Gitrub\Domain\Repository\Exception\RepositoryGithubGatewayError;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryGithubGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryScrapeStateGateway;

class ScrapeRepositoryUseCase {

	public function __construct(
		private RepositoryGateway $repository_gateway,
		private RepositoryGithubGateway $repository_github_gateway,
		private RepositoryScrapeStateGateway $repository_scrape_state_gateway,
	) {}

	/** @throws RepositoryGithubGatewayError */
	public function continueScrappingRepositories(int $limit = 50): void {
		$repositories_with_saved_last_element = new IteratorWithLastElementSaved(
			$this->repository_github_gateway->listRepositories(
				new FromLimit(
					from: ($this->repository_scrape_state_gateway->getLastScrappedId() ?? 0) + 1,
					limit: $limit,
				)
			)
		);
		$this->repository_gateway->storeRepositories(
			new RepositoryCollection($repositories_with_saved_last_element),
		);
		$last_repository = $repositories_with_saved_last_element->getLastElement();
		if ($last_repository) {
			$this->repository_scrape_state_gateway->saveLastScrappedId($last_repository->id);
		}
	}

	/** @throws RepositoryGithubGatewayError */
	public function scrapeRepositoriesFromLimit(FromLimit $from_limit): void {
		$this->repository_gateway->storeRepositories(
			$this->repository_github_gateway->listRepositories($from_limit)
		);
	}
}
