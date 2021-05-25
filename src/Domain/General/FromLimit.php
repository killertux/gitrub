<?php

namespace Gitrub\Domain\General;

class FromLimit {

	/** @throws \InvalidArgumentException */
	public function __construct(
		public int $from,
		public int $limit,
	) {
		if ($this->limit < 0) {
			throw new \InvalidArgumentException('Limit must be a positive number');
		}
	}

	public static function default(): self {
		return new self(0, 50);
	}

	public function validateLimit(int $max_expected_limit): void {
		if ($this->limit > $max_expected_limit) {
			throw new \InvalidArgumentException('Limit must be lower or equal to ' . $max_expected_limit);
		}
	}
}
