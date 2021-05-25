<?php

namespace Gitrub\Domain\User\Gateway;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Exception\UserGithubGatewayError;

interface UserGithubGateway {

	/** @throws UserGithubGatewayError */
	public function listUsers(FromLimit $from_limit): UserCollection;
}
