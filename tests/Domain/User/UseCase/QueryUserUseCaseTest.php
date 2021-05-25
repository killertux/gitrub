<?php

namespace Test\Gitrub\Domain\User\UseCase;

use Gitrub\Domain\User\Exception\UserNotFound;
use Gitrub\Domain\User\UseCase\QueryUserUseCase;
use Test\Gitrub\Gateway\User\MockUserGateway;
use Test\Gitrub\GitrubTestCase;

class QueryUserUseCaseTest extends GitrubTestCase {

	public function testGetUserByLogin(): void {
		$user_1 = $this->faker->user();
		$user_2 = $this->faker->user();

		$mocked_gateway = new MockUserGateway([$user_1, $user_2]);
		$query_user = new QueryUserUseCase($mocked_gateway);

		self::assertEquals($user_1, $query_user->getUserByLogin($user_1->login));
		self::assertEquals($user_2, $query_user->getUserByLogin($user_2->login));
	}

	public function testGetUserById(): void {
		$user_1 = $this->faker->user();
		$user_2 = $this->faker->user();

		$mocked_gateway = new MockUserGateway([$user_1, $user_2]);
		$query_user = new QueryUserUseCase($mocked_gateway);

		self::assertEquals($user_1, $query_user->getUserById($user_1->id));
		self::assertEquals($user_2, $query_user->getUserById($user_2->id));
	}

	public function testGetUserByLoginForLoginNotFound_ShouldThrowException(): void {
		$this->expectException(UserNotFound::class);
		$this->expectExceptionMessage('User not found with login login-not-found');
		(new QueryUserUseCase(new MockUserGateway([])))->getUserByLogin('login-not-found');
	}

	public function testGetUserByIdForIdNotFound_ShouldThrowException(): void {
		$this->expectException(UserNotFound::class);
		$this->expectExceptionMessage('User not found with id -1');
		(new QueryUserUseCase(new MockUserGateway([])))->getUserById(-1);
	}
}
