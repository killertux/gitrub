<?php

namespace Gitrub\App\Web\Rest\User\Controller\Presenter;

use Gitrub\App\Web\Rest\Response\AsResponse;
use Gitrub\App\Web\Rest\Response\Response;
use Gitrub\Domain\User\Entity\User;

class UserPresenter implements AsResponse {

	public function __construct(
		private User $user,
	) {}

	public function asResponse(): Response {
		return new Response(
			httpCode: 200,
			body: json_encode($this->user)
		);
	}
}
