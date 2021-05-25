<?php

namespace Gitrub\Domain\User\UseCase;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Gateway\UserGateway;

class ListUserUseCase {

	public function __construct(
		private UserGateway $user_gateway,
	) {}

	public function listUsers(FromLimit $from_limit_max_500): UserCollection {
		$this->validateLimit($from_limit_max_500);
		return $this->user_gateway->listUsers($from_limit_max_500);
	}

	public function listAdminUsers(FromLimit $from_limit_max_500): UserCollection {
		$this->validateLimit($from_limit_max_500);
		return $this->user_gateway->listAdminUsers($from_limit_max_500);
	}

	private function validateLimit(FromLimit $from_limit): void {
		$from_limit->validateLimit(max_expected_limit: 500);
	}
}
