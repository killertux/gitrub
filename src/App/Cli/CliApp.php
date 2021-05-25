<?php

namespace Gitrub\App\Cli;

use Gitrub\App\GatewayInstances;

class CliApp {

	public function __construct(
		private ?GatewayInstances $gateway_instances = null,
	) {}

	public function run(array $arguments): string {
		$migration_handler = new MigrationHandler();
		return match ($arguments[0] ?? '') {
			'scrapeUsers' => $this->scrapeUsers($arguments[1]),
			'scrapeRepositories' => $this->scrapeRepositories($arguments[1]),
			'migrate' => $migration_handler->migrate(),
			'reset' => $migration_handler->reset($arguments[1] ?? null),
			default => $this->helpMessage(),
		};
	}

	private function helpMessage(): string {
		return <<<HELP
Gitrub CLI

Available commands:

scrapeUsers [limit]                    Scrape new users from github and save them.
scrapeRepositories [limit]             Scrape new repositories from github and save them.
migrate                                Execute migrations
reset [test]                           Reset the database
HELP;

	}

	private function scrapeUsers(?string $arguments): string {
		$this->gateway_instances ??= GatewayInstances::default();
		return (new ScrapeUsersHandler(
			user_gateway: $this->gateway_instances->user_gateway,
			user_github_gateway: $this->gateway_instances->user_github_gateway,
			user_scrape_state_gateway: $this->gateway_instances->user_scrape_state_gateway
		))->scrape($arguments ?? null);
	}

	private function scrapeRepositories(?string $arguments): string {
		$this->gateway_instances ??= GatewayInstances::default();
		return (new ScrapeRepositoriesHandler(
			repository_gateway: $this->gateway_instances->repository_gateway,
			repository_github_gateway: $this->gateway_instances->repository_github_gateway,
			repository_scrape_state_gateway: $this->gateway_instances->repository_scrape_state_gateway,
		))->scrape($arguments ?? null);
	}
}
