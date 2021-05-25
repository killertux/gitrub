<?php

namespace Gitrub\Gateway\Github\RestApi;

class GithubPersonalAccessToken {

	public function __construct(
		private ?string $username,
		private ?string $token,
	) {}

	public function shouldUseToken(): bool {
		return !(empty($this->token) || empty($this->username));
	}

	public function getAuthorizationToken(): string {
		return 'Basic ' . base64_encode(
			$this->username . ':' . $this->token
		);
	}

	public static function createEmpty(): self {
		return new self(
			null,
			null
		);
	}
}
