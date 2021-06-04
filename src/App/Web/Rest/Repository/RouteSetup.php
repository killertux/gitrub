<?php

namespace Gitrub\App\Web\Rest\Repository;

use Gitrub\App\Web\Rest\Repository\Controller\ListRepositoryController;
use Gitrub\App\Web\Rest\Repository\Controller\QueryRepositoryController;
use Gitrub\App\Web\Rest\Repository\Controller\ScrapeRepositoryController;
use Gitrub\App\Web\Rest\Response\ResponseHandler;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryGithubGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryScrapeStateGateway;
use Steampixel\Route;

class RouteSetup {

	public function __construct(
		private RepositoryGateway $repository_gateway,
		private RepositoryGithubGateway $repository_github_gateway,
		private RepositoryScrapeStateGateway $repository_scrape_state_gateway,
		private ResponseHandler $response_handler,
	) {}

	public function setup(): void {
		$list_repositories_controller = new ListRepositoryController($this->repository_gateway);
		$query_repository_controller = new QueryRepositoryController($this->repository_gateway);
		$scrape_repository_controller = new ScrapeRepositoryController($this->repository_gateway, $this->repository_github_gateway, $this->repository_scrape_state_gateway);

		Route::add('/repositories', $this->handle([$list_repositories_controller, 'listRepositories']));
		Route::add('/forkRepositories', $this->handle([$list_repositories_controller, 'listForkRepositories']));
		Route::add('/repositoriesFromOwner/([0-9]*)', $this->handle([$list_repositories_controller, 'listRepositoriesFromOwner']));
		Route::add('/repositoriesWithName/([A-Za-z-0-9]*)', $this->handle([$list_repositories_controller, 'listRepositoriesWithName']));
		Route::add('/repository/([0-9]*)', $this->handle([$query_repository_controller, 'getRepositoryById']));
		Route::add('/repositoryWithFullName/(.*)', $this->handle([$query_repository_controller, 'getRepositoryByFullName']));
		Route::add('/repository/scrape', $this->handle([$scrape_repository_controller, 'scrapeRepositories']), 'post');
	}

	private function handle(callable $callable): callable {
		return fn (...$params) => $this->response_handler->handle($callable(...$params));
	}
}
