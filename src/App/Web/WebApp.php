<?php

namespace Gitrub\App\Web;

use Gitrub\App\GatewayInstances;
use Gitrub\App\Web\Response\Response;
use Gitrub\App\Web\Response\ResponseHandler;
use Gitrub\App\Web\User;
use Steampixel\Route;

class WebApp {

	public function __construct(
		private GatewayInstances $gateway_instances,
		private ResponseHandler $response_handler,
	) {}

	public function run(): void {
		(new User\RouteSetup(
			user_gateway: $this->gateway_instances->user_gateway,
			user_github_gateway: $this->gateway_instances->user_github_gateway,
			user_scrape_state_gateway: $this->gateway_instances->user_scrape_state_gateway,
			response_handler: $this->response_handler,
		))->setup();
		(new Repository\RouteSetup(
			repository_gateway: $this->gateway_instances->repository_gateway,
			repository_github_gateway: $this->gateway_instances->repository_github_gateway,
			repository_scrape_state_gateway: $this->gateway_instances->repository_scrape_state_gateway,
			response_handler: $this->response_handler,
		))->setup();
		$this->handlePathAndMethodNotFound();

		Route::run('', true, false, true);
	}

	private function handlePathAndMethodNotFound(): void {
		Route::pathNotFound(function (string $path) {
			$this->response_handler->handle(
				new Response(
					httpCode: 404,
					body: json_encode(['error' => "Can not execute $path"])
				)
			);
		});

		Route::methodNotAllowed(function (string $path, string $method) {
			$this->response_handler->handle(
				new Response(
					httpCode: 405,
					body: json_encode(['error' => "Can not execute $path with method $method"])
				)
			);
		});
	}
}
