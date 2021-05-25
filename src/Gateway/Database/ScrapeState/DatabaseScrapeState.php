<?php

namespace Gitrub\Gateway\Database\ScrapeState;

use Gitrub\Domain\Repository\Gateway\RepositoryScrapeStateGateway;
use Gitrub\Domain\User\Gateway\UserScrapeStateGateway;

class DatabaseScrapeState implements UserScrapeStateGateway, RepositoryScrapeStateGateway {

	public function __construct(
		private \PDO $connection,
		private string $scraper_name,
	) {}

	public function getLastScrappedId(): ?int {
		$sql = <<<SQL
SELECT last_scraped_id FROM scrape_state WHERE scraper_name = :scraper_name;
SQL;
		$statement = $this->connection->prepare($sql);
		$statement->execute([':scraper_name' => $this->scraper_name,]);

		return $statement->rowCount() != 1 ? null : $statement->fetch()['last_scraped_id'];
	}

	public function saveLastScrappedId(int $id): void {
		$sql = <<<SQL
INSERT INTO scrape_state (scraper_name, last_scraped_id)
VALUES (:scraper_name, :last_scraped_id)
ON DUPLICATE KEY UPDATE scraper_name =scraper_name, last_scraped_id = last_scraped_id;
SQL;
		$statement = $this->connection->prepare($sql);
		$statement->execute([
			':scraper_name' => $this->scraper_name,
			':last_scraped_id' => $id,
		]);
	}
}
