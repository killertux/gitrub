<?php

namespace Gitrub\App\Web;

use Gitrub\Domain\General\FromLimit;

class FromLimitFromQuery {

	public function __construct(
		private int $default_from,
		private int $default_limit,
	) {}

	/** @throws \InvalidArgumentException */
	public function fromLimit(): FromLimit {
		return new FromLimit(
			$_GET['from'] ?? $this->default_from,
			$_GET['limit'] ?? $this->default_limit,
		);
	}
}
