<?php

namespace Gitrub\App\Web\User\Controller\Presenter;

use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Response\Response;
use Gitrub\Domain\User\Exception\UserNotFound;

class UserNotFoundPresenter implements AsResponse {

	public function __construct(
		private UserNotFound $user_not_found
	) {}

	public function asResponse(): Response {
		return new Response(
			httpCode: 404,
			body: json_encode(['error' => $this->user_not_found->getMessage()]),
		);
	}
}
