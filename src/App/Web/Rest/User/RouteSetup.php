<?php

namespace Gitrub\App\Web\Rest\User;

use Gitrub\App\Web\Rest\Presenter\InvalidArgumentExceptionPresenter;
use Gitrub\App\Web\Rest\User\Controller\ListUserController;
use Gitrub\App\Web\Rest\User\Controller\Presenter\UserGithubGatewayErrorPresenter;
use Gitrub\App\Web\Rest\User\Controller\Presenter\UserNotFoundPresenter;
use Gitrub\App\Web\Rest\User\Controller\QueryUserController;
use Gitrub\App\Web\Rest\User\Controller\ScrapeUserController;
use Gitrub\App\Web\Router\Router;
use Gitrub\Domain\User\Exception\UserGithubGatewayError;
use Gitrub\Domain\User\Exception\UserNotFound;
use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Domain\User\Gateway\UserGithubGateway;
use Gitrub\Domain\User\Gateway\UserScrapeStateGateway;

class RouteSetup {

	public function __construct(
	    private Router $router,
		private UserGateway $user_gateway,
		private UserGithubGateway $user_github_gateway,
		private UserScrapeStateGateway $user_scrape_state_gateway,
	) {}

	public function setup(): void {
		$list_user_controller = new ListUserController($this->user_gateway);
		$query_user_controller = new QueryUserController($this->user_gateway);
		$scrape_user_controller = new ScrapeUserController($this->user_gateway, $this->user_github_gateway, $this->user_scrape_state_gateway);

		$this->router->addRoute('/users', [$list_user_controller, 'listUsers'])
            ->addRoute('/users', [$list_user_controller, 'listUsers'])
            ->addRoute('/adminUsers', [$list_user_controller, 'listAdminUsers'])
            ->addRoute('/user/([0-9]*)', [$query_user_controller, 'getUserById'])
            ->addRoute('/user/login/([A-Za-z-0-9]*)',[$query_user_controller, 'getUserByLogin'])
            ->addRoute('/user/scrape', [$scrape_user_controller, 'scrapeUsers'], 'POST')
            ->addExceptionPresenter(\InvalidArgumentException::class, InvalidArgumentExceptionPresenter::class)
            ->addExceptionPresenter(UserNotFound::class, UserNotFoundPresenter::class)
            ->addExceptionPresenter(UserGithubGatewayError::class, UserGithubGatewayErrorPresenter::class);
	}
}
