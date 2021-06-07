<?php

namespace Gitrub\App\Web;

use Gitrub\App\GatewayInstances;
use Gitrub\App\Web\Response\ResponseHandler;
use Gitrub\App\Web\Rest\RestApp;
use Gitrub\App\Web\Router\Router;

class WebApp {

	public function __construct(
		private GatewayInstances $gateway_instances,
		private ResponseHandler $response_handler,
	) {}

	public function run(): void {
	    $router = new Router($this->response_handler);
		(new RestApp($router, $this->gateway_instances))->setup();
		$router->run();
	}
}
