<?php

namespace Gitrub\Domain\Repository\Gateway;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Gitrub\Domain\Repository\Exception\RepositoryGithubGatewayError;

interface RepositoryGithubGateway {

	/** @throws RepositoryGithubGatewayError */
	public function listRepositories(FromLimit $from_limit): RepositoryCollection;
}
