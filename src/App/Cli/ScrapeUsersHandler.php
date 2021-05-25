<?php

namespace Gitrub\App\Cli;

use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Domain\User\Gateway\UserGithubGateway;
use Gitrub\Domain\User\Gateway\UserScrapeStateGateway;
use Gitrub\Domain\User\UseCase\ScrapeUserUseCase;

class ScrapeUsersHandler {

	public function __construct(
		private UserGateway $user_gateway,
		private UserGithubGateway $user_github_gateway,
		private UserScrapeStateGateway $user_scrape_state_gateway,
	) {}

	public function scrape(?int $limit): string {
		(new ScrapeUserUseCase($this->user_gateway, $this->user_github_gateway, $this->user_scrape_state_gateway))
			->continueScrappingUsers($limit ?? 50);
		return 'Users scrapped!';
	}
}
