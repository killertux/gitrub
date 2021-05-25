<?php

namespace Gitrub\Domain\User\Gateway;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Entity\User;
use Gitrub\Domain\User\Exception\UserNotFound;

interface UserGateway {

	public function listUsers(FromLimit $from_limit): UserCollection;
	public function listAdminUsers(FromLimit $from_limit): UserCollection;
	/** @throws UserNotFound */
	public function getUserByLogin(string $login): User;
	/** @throws UserNotFound */
	public function getUserById(int $id): User;
	public function storeUsers(UserCollection $users): void;
}
