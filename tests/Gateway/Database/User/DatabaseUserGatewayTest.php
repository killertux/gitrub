<?php

namespace Test\Gitrub\Gateway\Database\User;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Exception\UserNotFound;
use Gitrub\Gateway\Database\User\DatabaseUserGateway;
use Test\Gitrub\GitrubTestCase;
use Test\Gitrub\Support\Traits\DatabaseRestore;

class DatabaseUserGatewayTest extends GitrubTestCase {

	use DatabaseRestore;

	public function testStoreAndListUsers(): void {
		$users = [$this->faker->user(), $this->faker->user(), $this->faker->user()];

		$gateway = new DatabaseUserGateway($this->pdo);
		$gateway->storeUsers(new UserCollection(new \ArrayIterator($users)));
		$users_from_gateway = $gateway->listUsers(FromLimit::default());

		self::assertEquals(
			$users,
			iterator_to_array($users_from_gateway)
		);
	}

	public function testListUsersFromAndLimit(): void {
		$users = [$this->faker->user(), $this->faker->user(), $this->faker->user(), $this->faker->user()];

		$gateway = new DatabaseUserGateway($this->pdo);
		$gateway->storeUsers(new UserCollection(new \ArrayIterator($users)));
		$users_from_gateway = $gateway->listUsers(
			new FromLimit(from: $users[1]->id, limit: 2)
		);

		self::assertEquals(
			array_slice($users, 1, 2),
			iterator_to_array($users_from_gateway)
		);
	}

	public function testListAdminUsersFromLimit(): void {
		$normal_users = [$this->faker->user(), $this->faker->user()];
		$admin_users = [$this->faker->adminUser()];
		$normal_users_2 = [$this->faker->user()];
		$admin_users_2 = [$this->faker->adminUser(), $this->faker->adminUser()];
		$normal_users_3 = [$this->faker->user()];

		$gateway = new DatabaseUserGateway($this->pdo);
		$gateway->storeUsers(new UserCollection(
			new \ArrayIterator(array_merge($normal_users, $normal_users_2, $normal_users_3, $admin_users, $admin_users_2))
		));
		$users_from_gateway = $gateway->listAdminUsers(
			new FromLimit(from: $admin_users[0]->id, limit: 2)
		);

		self::assertEquals(
			[$admin_users[0], $admin_users_2[0]],
			iterator_to_array($users_from_gateway)
		);
	}

	public function testGetUserByLogin(): void {
		$users = [$this->faker->user(), $this->faker->user()];

		$gateway = new DatabaseUserGateway($this->pdo);
		$gateway->storeUsers(new UserCollection(new \ArrayIterator($users)));

		self::assertEquals($users[0], $gateway->getUserByLogin($users[0]->login));
		self::assertEquals($users[1], $gateway->getUserByLogin($users[1]->login));
	}

	public function testGetUserById(): void {
		$users = [$this->faker->user(), $this->faker->user()];

		$gateway = new DatabaseUserGateway($this->pdo);
		$gateway->storeUsers(new UserCollection(new \ArrayIterator($users)));

		self::assertEquals($users[0], $gateway->getUserById($users[0]->id));
		self::assertEquals($users[1], $gateway->getUserById($users[1]->id));
	}

	public function testUserNotFoundByLogin(): void {
		$this->expectException(UserNotFound::class);
		$this->expectExceptionMessage('User not found with login invalid-user');

		$gateway = new DatabaseUserGateway($this->pdo);
		$gateway->storeUsers(new UserCollection(new \ArrayIterator([])));
		$gateway->getUserByLogin('invalid-user');
	}

	public function testUserNotFoundById(): void {
		$this->expectException(UserNotFound::class);
		$this->expectExceptionMessage('User not found with id -1');

		$gateway = new DatabaseUserGateway($this->pdo);
		$gateway->storeUsers(new UserCollection(new \ArrayIterator([])));
		$gateway->getUserById(-1);
	}
}
