<?php

namespace Gitrub\App\Web\Rest\User\Controller;

use Gitrub\App\Web\Request\Request;
use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Rest\FromLimitFromRequest;
use Gitrub\App\Web\Rest\User\Controller\Presenter\UserCollectionPresenter;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Domain\User\UseCase\ListUserUseCase;

class ListUserController {

	public function __construct(
		private UserGateway $user_gateway,
	) {}

	public function listUsers(Request $request): AsResponse {
	    return new UserCollectionPresenter(
	        (new ListUserUseCase($this->user_gateway))
                ->listUsers($this->createFromLimit($request))
        );
	}

	public function listAdminUsers(Request $request): AsResponse {
        return new UserCollectionPresenter(
            (new ListUserUseCase($this->user_gateway))
                ->listADminUsers($this->createFromLimit($request))
        );
	}

	private function createFromLimit(Request $request): FromLimit {
		return (new FromLimitFromRequest(
			default_from: 0,
			default_limit: 50
		))->fromLimit($request);
	}
}
