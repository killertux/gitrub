<?php

namespace Gitrub\App\Web\Response;

class Response implements AsResponse {

	public function __construct(
		public int $httpCode,
		public string $body,
	) {}

	public function asResponse(): Response {
		return $this;
	}
}
