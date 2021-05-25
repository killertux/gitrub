<?php

namespace Test\Gitrub\Domain\General;

use Gitrub\Domain\General\FromLimit;
use Test\Gitrub\GitrubTestCase;

class FromLimitTest extends GitrubTestCase {

	public function testLimitCannotBeNegative(): void {
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Limit must be a positive number');
		(new FromLimit(0, -1));
	}

	/** @doesNotPerformAssertions */
	public function testValid(): void {
		(new FromLimit(0, 1))
			->validateLimit(1);
	}

	public function testValidInInvalid(): void {
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Limit must be lower or equal to 1');
		(new FromLimit(0, 2))
			->validateLimit(1);
	}
}
