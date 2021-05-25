<?php

namespace Test\Gitrub\Gateway\Database\ScrapeState;

use Gitrub\Gateway\Database\ScrapeState\DatabaseScrapeState;
use Test\Gitrub\GitrubTestCase;
use Test\Gitrub\Support\Traits\DatabaseRestore;

class DatabaseScrapeStateTest extends GitrubTestCase {

	use DatabaseRestore;

	public function testGetLastScrappedIdNeverInserted(): void {
		self::assertNull((new DatabaseScrapeState($this->pdo, 'test'))->getLastScrappedId());
	}

	public function testSaveAndGetScrappedId(): void {
		$gateway = new DatabaseScrapeState($this->pdo, 'test');
		$gateway->saveLastScrappedId(42);
		self::assertEquals(42, $gateway->getLastScrappedId());
	}
}
