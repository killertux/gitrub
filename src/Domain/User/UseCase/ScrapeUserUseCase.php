<?php

namespace Gitrub\Domain\User\UseCase;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\General\IteratorWithLastElementSaved;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Exception\UserGithubGatewayError;
use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Domain\User\Gateway\UserGithubGateway;
use Gitrub\Domain\User\Gateway\UserScrapeStateGateway;

class ScrapeUserUseCase {

	public function __construct(
		private UserGateway $user_gateway,
		private UserGithubGateway $user_github_gateway,
		private UserScrapeStateGateway $user_scrape_state_gateway,
	) {}

	/** @throws UserGithubGatewayError */
	public function continueScrappingUsers(int $limit = 50): void {
		$users_with_saved_last_element = new IteratorWithLastElementSaved($this->user_github_gateway->listUsers(
			new FromLimit(
				from: ($this->user_scrape_state_gateway->getLastScrappedId() ?? 0) + 1,
				limit: $limit,
			)
		));
		$this->user_gateway->storeUsers(
			new UserCollection($users_with_saved_last_element),
		);
		$last_user = $users_with_saved_last_element->getLastElement();
		if ($last_user) {
			$this->user_scrape_state_gateway->saveLastScrappedId($last_user->id);
		}
	}

	/** @throws UserGithubGatewayError */
	public function scrapeUsersFromLimit(FromLimit $from_limit): void {
		$this->user_gateway->storeUsers(
			$this->user_github_gateway->listUsers(
				$from_limit
			)
		);
	}
}
