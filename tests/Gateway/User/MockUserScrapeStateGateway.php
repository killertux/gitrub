<?php

namespace Test\Gitrub\Gateway\User;

use Gitrub\Domain\User\Gateway\UserScrapeStateGateway;

class MockUserScrapeStateGateway implements UserScrapeStateGateway {

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
