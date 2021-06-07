<?php

namespace Test\Gitrub\App\Web\Rest\User\Controller;

use Gitrub\App\Web\Rest\User\Controller\Presenter\UserPresenter;
use Gitrub\App\Web\Rest\User\Controller\QueryUserController;
use Test\Gitrub\Gateway\User\MockUserGateway;
use Test\Gitrub\GitrubTestCase;

class QueryUserControllerTest extends GitrubTestCase {

	public function testGetUserByLogin(): void {
		$user = $this->faker->user();

		$response = (new QueryUserController(new MockUserGateway([$user])))
			->getUserByLogin($user->login)
			->asResponse();
		$expected_response = (new UserPresenter($user))->asResponse();

		self::assertEquals($expected_response, $response);
	}

	public function testGetUserById(): void {
		$user = $this->faker->user();

		$response = (new QueryUserController(new MockUserGateway([$user])))
			->getUserById($user->id)
			->asResponse();
		$expected_response = (new UserPresenter($user))->asResponse();

		self::assertEquals($expected_response, $response);
	}
}
