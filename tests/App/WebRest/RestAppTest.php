<?php

namespace Test\Gitrub\App\WebRest;

use Gitrub\App\Web\Rest\Response\ResponseHandler;
use Gitrub\App\Web\WebApp;
use Test\Gitrub\App\WebRest\Response\MockResponseHandler;
use Test\Gitrub\GitrubTestCase;
use Test\Gitrub\Support\Traits\GetGlobalCleaner;
use Test\Gitrub\Support\Traits\ServerGlobalCleaner;

class RestAppTest extends GitrubTestCase {

	use ServerGlobalCleaner;
	use GetGlobalCleaner;

	public function testPathNotFound(): void {
		$response_handler = new MockResponseHandler();
		$web_app = $this->buildWebApp(response_handler: $response_handler);
		$this->fakeRequest('/invalid/path', 'GET');
		$web_app->run();
		self::assertEquals(404, $response_handler->last_response->httpCode);
		self::assertEquals('{"error":"Can not execute \/invalid\/path"}', $response_handler->last_response->body);
	}

	public function testMethodNotFound(): void {
		$response_handler = new MockResponseHandler();
		$web_app = $this->buildWebApp(response_handler: $response_handler);
		$this->fakeRequest('/users', 'POST');
		$web_app->run();
		self::assertEquals(405, $response_handler->last_response->httpCode);
		self::assertEquals('{"error":"Can not execute \/users with method POST"}', $response_handler->last_response->body);
	}

	public function testListUser(): void {
		$response_handler = new MockResponseHandler();
		$user = $this->faker->user();
		$web_app = $this->buildWebApp(response_handler: $response_handler, users: [$user]);

		$this->fakeRequest("/user/$user->id", 'GET');
		$web_app->run();

		self::assertEquals(200, $response_handler->last_response->httpCode);
		self::assertEquals(json_encode($user), $response_handler->last_response->body);
	}

	public function testGetRepository(): void {
		$response_handler = new MockResponseHandler();
		$repository = $this->faker->repository();
		$web_app = $this->buildWebApp(response_handler: $response_handler, repositories: [$repository]);

		$this->fakeRequest("/repository/$repository->id", 'GET');
		$web_app->run();

		self::assertEquals(200, $response_handler->last_response->httpCode);
		self::assertEquals(json_encode($repository), $response_handler->last_response->body);
	}

	private function buildWebApp(ResponseHandler $response_handler, array $users = [], array $repositories = []): WebApp {
		return new WebApp(
			$this->faker->gatewayInstances(users: $users, repositories: $repositories),
			$response_handler,
		);
	}

	private function fakeRequest(string $uri, $method): void {
		$_SERVER['REQUEST_URI'] = $uri;
		$_SERVER['REQUEST_METHOD'] = $method;
	}
}
