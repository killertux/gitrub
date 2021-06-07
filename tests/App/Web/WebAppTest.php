<?php

namespace Test\Gitrub\App\Web;

use Test\Gitrub\App\Web\Response\MockResponseHandler;
use Test\Gitrub\GitrubTestCase;
use Test\Gitrub\Support\Traits\FakeRequest;

class WebAppTest extends GitrubTestCase {

    use FakeRequest;

	public function testPathNotFound(): void {
		$response_handler = new MockResponseHandler();
		$web_app = $this->buildWebApp(response_handler: $response_handler);
		$this->fakeRequest($web_app, '/invalid/path', 'GET');
		self::assertEquals(404, $response_handler->last_response->http_code);
		self::assertEquals('{"error":"Can not execute \/invalid\/path"}', $response_handler->last_response->body);
	}

	public function testMethodNotFound(): void {
		$response_handler = new MockResponseHandler();
		$web_app = $this->buildWebApp(response_handler: $response_handler);
		$this->fakeRequest($web_app, '/users', 'POST');
		self::assertEquals(405, $response_handler->last_response->http_code);
		self::assertEquals('{"error":"Can not execute \/users with method POST"}', $response_handler->last_response->body);
	}

	public function testListUser(): void {
		$response_handler = new MockResponseHandler();
		$user = $this->faker->user();
		$web_app = $this->buildWebApp(response_handler: $response_handler, users: [$user]);

		$this->fakeRequest($web_app, "/user/$user->id", 'GET');

		self::assertEquals(200, $response_handler->last_response->http_code);
		self::assertEquals(json_encode($user), $response_handler->last_response->body);
	}

	public function testGetRepository(): void {
		$response_handler = new MockResponseHandler();
		$repository = $this->faker->repository();
		$web_app = $this->buildWebApp(response_handler: $response_handler, repositories: [$repository]);
		$this->fakeRequest($web_app, "/repository/$repository->id", 'GET');

		self::assertEquals(200, $response_handler->last_response->http_code);
		self::assertEquals(json_encode($repository), $response_handler->last_response->body);
	}
}
