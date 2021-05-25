<?php

namespace Gitrub\App\Web\User;

use Gitrub\App\Web\Response\ResponseHandler;
use Gitrub\App\Web\User\Controller\ListUserController;
use Gitrub\App\Web\User\Controller\QueryUserController;
use Gitrub\App\Web\User\Controller\ScrapeUserController;
use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Domain\User\Gateway\UserGithubGateway;
use Gitrub\Domain\User\Gateway\UserScrapeStateGateway;
use Steampixel\Route;

class RouteSetup {

	public function __construct(
		private UserGateway $user_gateway,
		private UserGithubGateway $user_github_gateway,
		private UserScrapeStateGateway $user_scrape_state_gateway,
		private ResponseHandler $response_handler,
	) {}

	public function setup(): void {
		$list_user_controller = new ListUserController($this->user_gateway);
		$query_user_controller = new QueryUserController($this->user_gateway);
		$scrape_user_controller = new ScrapeUserController($this->user_gateway, $this->user_github_gateway, $this->user_scrape_state_gateway);

		Route::add('/users', $this->handle([$list_user_controller, 'listUsers']));
		Route::add('/adminUsers', $this->handle([$list_user_controller, 'listAdminUsers']));
		Route::add('/user/([0-9]*)', $this->handle([$query_user_controller, 'getUserById']));
		Route::add('/user/login/([A-Za-z-0-9]*)', $this->handle([$query_user_controller, 'getUserByLogin']));
		Route::add('/user/scrape', $this->handle([$scrape_user_controller, 'scrapeUsers']), 'post');
	}

	private function handle(callable $callable): callable {
		return fn (...$params) => $this->response_handler->handle($callable(...$params));
	}
}
