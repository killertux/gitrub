<?php

namespace Gitrub\App\Web\Rest;

use Gitrub\App\GatewayInstances;
use Gitrub\App\Web\Response\Response;
use Gitrub\App\Web\Response\ResponseHandler;
use Gitrub\App\Web\Rest\User;
use Steampixel\Route;

class RestApp {

	public function __construct(
		private GatewayInstances $gateway_instances,
		private ResponseHandler $response_handler,
	) {}

	public function setup(): void {
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
	}
}
