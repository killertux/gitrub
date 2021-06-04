<?php

namespace Test\Gitrub\App\WebRest\User\Controller;

use EBANX\Stream\Stream;
use Gitrub\App\Web\Rest\User\Controller\ListUserController;
use Gitrub\App\Web\Rest\User\Controller\Presenter\UserCollectionPresenter;
use Gitrub\Domain\User\Collection\UserCollection;
use Test\Gitrub\Gateway\User\MockUserGateway;
use Test\Gitrub\GitrubTestCase;
use Test\Gitrub\Support\Traits\GetGlobalCleaner;

class ListUserControllerTest extends GitrubTestCase {

	use GetGlobalCleaner;

	public function testListUsersWithDefaultParams(): void {
		$users = $this->faker->createsABunchOfUsers(n_users: 51);
		$response = (new ListUserController(new MockUserGateway($users)))
			->listUsers()
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
		$_GET['from'] = $users[0]->id + 1;
		$_GET['limit'] = 10;

		$response = (new ListUserController(new MockUserGateway($users)))
			->listUsers()
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
			->listAdminUsers()
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
			->listAdminUsers()
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
