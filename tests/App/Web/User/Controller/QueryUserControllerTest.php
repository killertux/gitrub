<?php

namespace Test\Gitrub\App\Web\User\Controller;

use Gitrub\App\Web\User\Controller\Presenter\UserNotFoundPresenter;
use Gitrub\App\Web\User\Controller\Presenter\UserPresenter;
use Gitrub\App\Web\User\Controller\QueryUserController;
use Gitrub\Domain\User\Exception\UserNotFound;
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

	public function testGetUserByLoginNotFound(): void {
		$response = (new QueryUserController(new MockUserGateway([])))
			->getUserByLogin('invalid-login')
			->asResponse();
		$expected_response = (new UserNotFoundPresenter(
			new UserNotFound('User not found with login invalid-login')
		))->asResponse();

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

	public function testGetUserByIdNotFound(): void {
		$response = (new QueryUserController(new MockUserGateway([])))
			->getUserById(-1)
			->asResponse();
		$expected_response = (new UserNotFoundPresenter(
			new UserNotFound('User not found with id -1')
		))->asResponse();

		self::assertEquals($expected_response, $response);
	}
}
