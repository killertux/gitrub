<?php

namespace Gitrub\Domain\Repository\Gateway;

interface RepositoryScrapeStateGateway {

	public function getLastScrappedId(): ?int;
	public function saveLastScrappedId(int $id): void;
}
