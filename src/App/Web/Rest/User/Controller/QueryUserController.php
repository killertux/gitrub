<?php

namespace Gitrub\App\Web\Rest\User\Controller;

use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Rest\User\Controller\Presenter\UserPresenter;
use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Domain\User\UseCase\QueryUserUseCase;

class QueryUserController {

	public function __construct(
		private UserGateway $user_gateway,
	) {}

	public function getUserByLogin(string $login): AsResponse {
		return new UserPresenter((new QueryUserUseCase($this->user_gateway))->getUserByLogin($login));
	}

	public function getUserById(string $id): AsResponse {
		return new UserPresenter((new QueryUserUseCase($this->user_gateway))->getUserById($id));
	}
}
