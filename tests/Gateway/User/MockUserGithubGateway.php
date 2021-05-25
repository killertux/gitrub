<?php

namespace Test\Gitrub\Gateway\User;

use EBANX\Stream\Stream;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Entity\User;
use Gitrub\Domain\User\Gateway\UserGithubGateway;

class MockUserGithubGateway implements UserGithubGateway {

	/** @var User[]  */
	private array $users;

	public function __construct(array $users) {
		usort($users, fn (User $user_a, User $user_b) => $user_a->id <=> $user_b->id);
		$this->users = $users;
	}

	public function listUsers(FromLimit $from_limit): UserCollection {
		return new UserCollection(
			Stream::of($this->users)
				->filter(fn(User $user) => $user->id >= $from_limit->from)
				->take($from_limit->limit)
		);
	}
}
