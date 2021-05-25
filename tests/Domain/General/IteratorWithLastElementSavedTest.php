<?php

namespace Test\Gitrub\Domain\General;

use EBANX\Stream\Stream;
use Gitrub\Domain\General\IteratorWithLastElementSaved;
use Test\Gitrub\GitrubTestCase;

class IteratorWithLastElementSavedTest extends GitrubTestCase {

	public function testIteratorNeverConsumed(): void {
		$iterator = new IteratorWithLastElementSaved(
			Stream::rangeInt(1, 5),
		);
		self::assertNull($iterator->getLastElement());
	}

	public function testIteratorConsumed_ShouldGetLastElement(): void {
		$iterator = new IteratorWithLastElementSaved(
			Stream::rangeInt(1, 5),
		);
		iterator_to_array($iterator);
		self::assertEquals(5, $iterator->getLastElement());
	}
}
