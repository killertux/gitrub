<?php

namespace Gitrub\App\Web\User\Controller\Presenter;

use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Response\Response;
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
