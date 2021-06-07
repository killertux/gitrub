<?php

namespace Gitrub\App\Web\Response;

class Response implements AsResponse {

	public function __construct(
		public int $http_code,
		public string $body,
	) {}

	public function asResponse(): Response {
		return $this;
	}
}
