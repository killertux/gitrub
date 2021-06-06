<?php

namespace Gitrub\App\Web\Rest\Presenter;

use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Response\Response;

class InvalidArgumentExceptionPresenter implements AsResponse {

	public function __construct(
		private \InvalidArgumentException $invalid_argument_exception
	) {}

	public function asResponse(): Response {
		return new Response(
			httpCode: 400,
			body: json_encode(
				['error' => $this->invalid_argument_exception->getMessage()]
			),
		);
	}
}
