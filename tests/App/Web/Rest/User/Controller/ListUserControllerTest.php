<?php

namespace Test\Gitrub\App\Web\Rest\User\Controller;

use EBANX\Stream\Stream;
use Gitrub\App\Web\Request\Request;
use Gitrub\App\Web\Rest\User\Controller\ListUserController;
use Gitrub\App\Web\Rest\User\Controller\Presenter\UserCollectionPresenter;
use Gitrub\Domain\User\Collection\UserCollection;
use Test\Gitrub\Gateway\User\MockUserGateway;
use Test\Gitrub\GitrubTestCase;

class ListUserControllerTest extends GitrubTestCase {

	public function testListUsersWithDefaultParams(): void {
		$users = $this->faker->createsABunchOfUsers(n_users: 51);
		$response = (new ListUserController(new MockUserGateway($users)))
			->listUsers(Request::empty())
			->asResponse();

		$expected_response = (new UserCollectionPresenter(
			new UserCollection(Stream::of($users)->take(50))
		))->asResponse();
		self::assertEquals(
			$expected_response->asResponse(),
			$response->asResponse()
		);
	}

	public function testListUsersPassingParams(): void {
		$users = $this->faker->createsABunchOfUsers(n_users: 12);

		$response = (new ListUserController(new MockUserGateway($users)))
			->listUsers(new Request(['from' => $users[0]->id + 1, 'limit' => 10]))
			->asResponse();

		$expected_response = (new UserCollectionPresenter(
			new UserCollection(Stream::of($users)->skip(1)->take(10))
		))->asResponse();
		self::assertEquals(
			$expected_response,
			$response
		);
	}

	public function testListAdminUsersWithDefaultParams(): void {
		$users = $this->faker->createsABunchOfAdminUsers(n_users: 51);
		$response = (new ListUserController(new MockUserGateway($users)))
			->listAdminUsers(Request::empty())
			->asResponse();

		$expected_response = (new UserCollectionPresenter(
			new UserCollection(Stream::of($users)->take(50))
		))->asResponse();
		self::assertEquals(
			$expected_response->asResponse(),
			$response->asResponse()
		);
	}

	public function testListAdminUsersPassingParams(): void {
		$users = $this->faker->createsABunchOfAdminUsers(n_users: 12);
		$_GET['from'] = $users[0]->id + 1;
		$_GET['limit'] = 10;

		$response = (new ListUserController(new MockUserGateway($users)))
			->listAdminUsers(new Request(['from' => $users[0]->id + 1, 'limit' => 10]))
			->asResponse();

		$expected_response = (new UserCollectionPresenter(
			new UserCollection(Stream::of($users)->skip(1)->take(10))
		))->asResponse();
		self::assertEquals(
			$expected_response,
			$response
		);
	}
}
