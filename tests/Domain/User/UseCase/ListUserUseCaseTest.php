<?php

namespace Test\Gitrub\Domain\User\UseCase;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\UseCase\ListUserUseCase;
use Test\Gitrub\Gateway\User\MockUserGateway;
use Test\Gitrub\GitrubTestCase;

class ListUserUseCaseTest extends GitrubTestCase {

	public function testListUsersWithEmptyGateway_ShouldReturnEmptyList(): void {
		$empty_gateway = new MockUserGateway([]);
		$users = (new ListUserUseCase($empty_gateway))->listUsers(FromLimit::default());
		self::assertEmpty(iterator_to_array($users));
	}

	public function testListUsers_ShouldReturnUsersFromGateway(): void {
		$mocked_users = [$this->faker->user(), $this->faker->user(),];
		$mock_gateway = new MockUserGateway($mocked_users);
		$users = (new ListUserUseCase($mock_gateway))->listUsers(FromLimit::default());
		self::assertEquals($mocked_users, iterator_to_array($users));
	}

	public function testListUsersAfterAnId(): void {
		$mocked_users = [$this->faker->user(), $this->faker->user(),];
		$mock_gateway = new MockUserGateway($mocked_users);
		$users = (new ListUserUseCase($mock_gateway))
			->listUsers(new FromLimit(from: $mocked_users[0]->id + 1, limit: 50));
		self::assertEquals([$mocked_users[1]], iterator_to_array($users));
	}

	public function testListUsersWithALimit(): void {
		$mocked_users = [$this->faker->user(), $this->faker->user(),];
		$mock_gateway = new MockUserGateway($mocked_users);
		$users = (new ListUserUseCase($mock_gateway))
			->listUsers(new FromLimit(from: $mocked_users[0]->id, limit: 1));
		self::assertEquals([$mocked_users[0]], iterator_to_array($users));
	}

	public function testListAdminUsersWithEmptyGateway_ShouldReturnEmptyList(): void {
		$empty_gateway = new MockUserGateway([]);
		$users = (new ListUserUseCase($empty_gateway))->listAdminUsers(FromLimit::default());
		self::assertEmpty(iterator_to_array($users));
	}

	public function testListAdminUsers_ShouldReturnOnlyAdminUsersFromGateway(): void {
		$normal_users = [$this->faker->user(), $this->faker->user(),];
		$admin_users = [$this->faker->adminUser(), $this->faker->adminUser()];
		$mock_gateway = new MockUserGateway(array_merge($normal_users, $admin_users));
		$users = (new ListUserUseCase($mock_gateway))->listAdminUsers(FromLimit::default());
		self::assertEquals($admin_users, iterator_to_array($users));
	}

	public function testListAdminUsersAfterAnId(): void {
		$mocked_admin_users = [$this->faker->adminUser(), $this->faker->adminUser()];
		$mock_gateway = new MockUserGateway($mocked_admin_users);
		$users = (new ListUserUseCase($mock_gateway))
			->listAdminUsers(new FromLimit(from: $mocked_admin_users[0]->id + 1, limit: 50));
		self::assertEquals([$mocked_admin_users[1]], iterator_to_array($users));
	}

	public function testListAdminUsersWithALimit(): void {
		$mocked_admin_users = [$this->faker->adminUser(), $this->faker->adminUser()];
		$mock_gateway = new MockUserGateway($mocked_admin_users);
		$users = (new ListUserUseCase($mock_gateway))
			->listAdminUsers(new FromLimit(from: $mocked_admin_users[0]->id, limit: 1));
		self::assertEquals([$mocked_admin_users[0]], iterator_to_array($users));
	}

	public static function dataProviderForTestInvalidLimits(): \Generator {
		yield 'over 500' => [
			'limit' => 501,
			'expected_exception_message' => 'Limit must be lower or equal to 500',
		];
		yield 'equal to 500' => [
			'limit' => 500,
			'expected_exception_message' => null,
		];
		yield 'equal to 1' => [
			'limit' => 1,
			'expected_exception_message' => null,
		];
		yield 'equal to 0' => [
			'limit' => 0,
			'expected_exception_message' => null,
		];

		yield 'smaller to 0' => [
			'limit' => -1,
			'expected_exception_message' => 'Limit must be a positive number',
		];
	}

	/** @dataProvider dataProviderForTestInvalidLimits */
	public function testListAdminUsersLimits(int $limit, ?string $expected_exception_message): void {
		if ($expected_exception_message !== null) {
			$this->expectException(\InvalidArgumentException::class);
			$this->expectExceptionMessage($expected_exception_message);
		} else {
			self::assertTrue(true);
		}
		(new ListUserUseCase(new MockUserGateway([])))
			->listAdminUsers(new FromLimit(from: 0, limit: $limit));
	}

	/** @dataProvider dataProviderForTestInvalidLimits */
	public function testListUsersLimits(int $limit, ?string $expected_exception_message): void {
		if ($expected_exception_message !== null) {
			$this->expectException(\InvalidArgumentException::class);
			$this->expectExceptionMessage($expected_exception_message);
		} else {
			self::assertTrue(true);
		}
		(new ListUserUseCase(new MockUserGateway([])))
			->listUsers(new FromLimit(from: 0, limit: $limit));
	}
}
