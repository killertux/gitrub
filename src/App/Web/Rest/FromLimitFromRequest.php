<?php

namespace Gitrub\App\Web\Rest;

use Gitrub\App\Web\Request\Request;
use Gitrub\Domain\General\FromLimit;

class FromLimitFromRequest {

	public function __construct(
		private int $default_from,
		private int $default_limit,
	) {}

	/** @throws \InvalidArgumentException */
	public function fromLimit(Request $request): FromLimit {
		return new FromLimit(
			$request->query['from'] ?? $this->default_from,
            $request->query['limit'] ?? $this->default_limit,
		);
	}
}
