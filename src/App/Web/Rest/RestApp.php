<?php

namespace Gitrub\App\Web\Rest;

use Gitrub\App\GatewayInstances;
use Gitrub\App\Web\Request\Request;
use Gitrub\App\Web\Rest\User;
use Gitrub\App\Web\Router\Router;

class RestApp {

	public function __construct(
	    private Router $router,
		private GatewayInstances $gateway_instances,
	) {}

	public function setup(): void {
		(new User\RouteSetup(
		    router: $this->router,
			user_gateway: $this->gateway_instances->user_gateway,
			user_github_gateway: $this->gateway_instances->user_github_gateway,
			user_scrape_state_gateway: $this->gateway_instances->user_scrape_state_gateway,
		))->setup();
		(new Repository\RouteSetup(
            router: $this->router,
			repository_gateway: $this->gateway_instances->repository_gateway,
			repository_github_gateway: $this->gateway_instances->repository_github_gateway,
			repository_scrape_state_gateway: $this->gateway_instances->repository_scrape_state_gateway,
		))->setup();
	}
}
