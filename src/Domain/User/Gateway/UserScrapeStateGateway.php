<?php

namespace Gitrub\Domain\User\Gateway;

interface UserScrapeStateGateway {

	public function getLastScrappedId(): ?int;
	public function saveLastScrappedId(int $id): void;
}
