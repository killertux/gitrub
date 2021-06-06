<?php

namespace Gitrub\App\Web\Rest\User\Controller;

use Gitrub\App\Web\Rest\FromLimitFromQuery;
use Gitrub\App\Web\Rest\Presenter\InvalidArgumentExceptionPresenter;
use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Rest\User\Controller\Presenter\UserCollectionPresenter;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Domain\User\UseCase\ListUserUseCase;

class ListUserController {

	public function __construct(
		private UserGateway $user_gateway,
	) {}

	public function listUsers(): AsResponse {
		return $this->executeAndHandleExceptions(
			fn() => new UserCollectionPresenter((new ListUserUseCase($this->user_gateway))
				->listUsers($this->createFromLimit()))
		);
	}

	public function listAdminUsers(): AsResponse {
		return $this->executeAndHandleExceptions(
			fn() => new UserCollectionPresenter((new ListUserUseCase($this->user_gateway))
				->listAdminUsers($this->createFromLimit()))
		);
	}

	private function executeAndHandleExceptions(callable $closure): AsResponse {
		try {
			return $closure();
		} catch (\InvalidArgumentException $invalid_argument_exception) {
			return new InvalidArgumentExceptionPresenter($invalid_argument_exception);
		}
	}

	private function createFromLimit(): FromLimit {
		return (new FromLimitFromQuery(
			default_from: 0,
			default_limit: 50
		))->fromLimit();
	}
}
