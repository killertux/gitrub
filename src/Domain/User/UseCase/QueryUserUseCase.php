<?php

namespace Gitrub\Domain\User\UseCase;

use Gitrub\Domain\User\Entity\User;
use Gitrub\Domain\User\Exception\UserNotFound;
use Gitrub\Domain\User\Gateway\UserGateway;

class QueryUserUseCase {

	public function __construct(
		private UserGateway $user_gateway,
	) {}

	/** @throws UserNotFound */
	public function getUserByLogin(string $login): User {
		return $this->user_gateway->getUserByLogin($login);
	}

	/** @throws UserNotFound */
	public function getUserById(int $id): User {
		return $this->user_gateway->getUserById($id);
	}
}
