<?php

namespace Test\Gitrub\Gateway\User;


use EBANX\Stream\Stream;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Entity\User;
use Gitrub\Domain\User\Exception\UserNotFound;
use Gitrub\Domain\User\Gateway\UserGateway;
use PHPUnit\Framework\Assert;

class MockUserGateway implements UserGateway {

	/** @var User[]  */
	private array $users = [];

	public function __construct(array $users) {
		$this->storeUsers(
			new UserCollection(new \ArrayIterator($users))
		);
	}

	public function listUsers(FromLimit $from_limit): UserCollection {
		return new UserCollection(
			Stream::of($this->users)
				->filter(fn(User $user) => $user->id >= $from_limit->from)
				->take($from_limit->limit)
		);
	}

	public function listAdminUsers(FromLimit $from_limit): UserCollection {
		return new UserCollection(
			Stream::of($this->users)
				->filter(fn(User $user) => $user->id >= $from_limit->from)
				->filter(fn(User $user) => $user->site_admin == true)
				->take($from_limit->limit)
		);
	}

	public function getUserByLogin(string $login): User {
			return Stream::of($this->users)
				->filter(fn(User $user) => $user->login == $login)
				->take(1)
				->collect()[0] ?? throw new UserNotFound("User not found with login $login");
	}

	public function getUserById(int $id): User {
		return Stream::of($this->users)
			->filter(fn(User $user) => $user->id == $id)
			->take(1)
			->collect()[0] ?? throw new UserNotFound("User not found with id $id");
	}

	public function storeUsers(UserCollection $users): void  {
		$this->users = array_merge($this->users, iterator_to_array($users));
		usort($this->users, fn(User $user_a, User $user_b) => $user_a->id <=> $user_b->id);
		$this->users = $this->unique($this->users);
	}

	public function assertEmpty(): void {
		Assert::assertEmpty($this->users);
	}

	public function assertSavedUsers(array $users): void {
		Assert::assertEquals($users, $this->users);
	}

	private static function unique(array $users): array {
		$returner = [];
		foreach ($users as $user) {
			$returner[$user->id] = $user;
		}
		return array_values($returner);
	}
}
