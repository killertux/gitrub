<?php

namespace Test\Gitrub\Gateway\Repository;

use Gitrub\Domain\Repository\Gateway\RepositoryScrapeStateGateway;

class MockRepositoryScrapeStateGateway implements RepositoryScrapeStateGateway {

	public function __construct(
		private ?int $last_scraped_id = null
	) {}

	public function getLastScrappedId(): ?int {
		return $this->last_scraped_id;
	}

	public function saveLastScrappedId(int $id): void {
		$this->last_scraped_id = $id;
	}
}
