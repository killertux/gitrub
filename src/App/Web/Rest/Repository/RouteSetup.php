<?php

namespace Gitrub\App\Web\Rest\Repository;

use Gitrub\App\Web\Rest\Presenter\InvalidArgumentExceptionPresenter;
use Gitrub\App\Web\Rest\Repository\Controller\ListRepositoryController;
use Gitrub\App\Web\Rest\Repository\Controller\Presenter\RepositoryGithubGatewayErrorPresenter;
use Gitrub\App\Web\Rest\Repository\Controller\Presenter\RepositoryNotFoundPresenter;
use Gitrub\App\Web\Rest\Repository\Controller\QueryRepositoryController;
use Gitrub\App\Web\Rest\Repository\Controller\ScrapeRepositoryController;
use Gitrub\App\Web\Router\Router;
use Gitrub\Domain\Repository\Exception\RepositoryGithubGatewayError;
use Gitrub\Domain\Repository\Exception\RepositoryNotFound;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryGithubGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryScrapeStateGateway;

class RouteSetup {

	public function __construct(
	    private Router $router,
		private RepositoryGateway $repository_gateway,
		private RepositoryGithubGateway $repository_github_gateway,
		private RepositoryScrapeStateGateway $repository_scrape_state_gateway,
	) {}

	public function setup(): void {
		$list_repositories_controller = new ListRepositoryController($this->repository_gateway);
		$query_repository_controller = new QueryRepositoryController($this->repository_gateway);
		$scrape_repository_controller = new ScrapeRepositoryController($this->repository_gateway, $this->repository_github_gateway, $this->repository_scrape_state_gateway);

		$this->router
            ->addRoute('/repositories', [$list_repositories_controller, 'listRepositories'])
		    ->addRoute('/forkRepositories', [$list_repositories_controller, 'listForkRepositories'])
		    ->addRoute('/repositoriesFromOwner/([0-9]*)',[$list_repositories_controller, 'listRepositoriesFromOwner'])
		    ->addRoute('/repositoriesWithName/([A-Za-z-0-9]*)', [$list_repositories_controller, 'listRepositoriesWithName'])
		    ->addRoute('/repository/([0-9]*)', [$query_repository_controller, 'getRepositoryById'])
		    ->addRoute('/repositoryWithFullName/(.*)', [$query_repository_controller, 'getRepositoryByFullName'])
		    ->addRoute('/repository/scrape', [$scrape_repository_controller, 'scrapeRepositories'], 'POST')
            ->addExceptionPresenter(\InvalidArgumentException::class, InvalidArgumentExceptionPresenter::class)
            ->addExceptionPresenter(RepositoryNotFound::class, RepositoryNotFoundPresenter::class)
            ->addExceptionPresenter(RepositoryGithubGatewayError::class, RepositoryGithubGatewayErrorPresenter::class);
	}
}
