<?php

namespace Gitrub\App\Web\Rest\User\Controller;

use Gitrub\App\Web\Rest\Presenter\InvalidArgumentExceptionPresenter;
use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Response\Response;
use Gitrub\App\Web\Rest\User\Controller\Presenter\UserGithubGatewayErrorPresenter;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Exception\UserGithubGatewayError;
use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Domain\User\Gateway\UserGithubGateway;
use Gitrub\Domain\User\Gateway\UserScrapeStateGateway;
use Gitrub\Domain\User\UseCase\ScrapeUserUseCase;

class ScrapeUserController {

	public function __construct(
		private UserGateway $user_gateway,
		private UserGithubGateway $user_github_gateway,
		private UserScrapeStateGateway $user_scrape_state_gateway,
	) {}

	public function scrapeUsers(): AsResponse {
		try {
			$from = $_GET['from'] ?? null;
			$limit = $_GET['limit'] ?? 100;

			$this->executeUseCase($from ? (int)$from : null, (int)$limit);
			return new Response(
				httpCode: 200,
				body: json_encode(['message' => 'done'])
			);
		} catch (\InvalidArgumentException $invalid_argument_exception) {
			return new InvalidArgumentExceptionPresenter($invalid_argument_exception);
		} catch (UserGithubGatewayError $user_github_gateway_error) {
			return new UserGithubGatewayErrorPresenter($user_github_gateway_error);
		}
	}

	/** @throws UserGithubGatewayError */
	private function executeUseCase(?int $from, int $limit): void {
		$use_case = new ScrapeUserUseCase($this->user_gateway, $this->user_github_gateway, $this->user_scrape_state_gateway);
		if ($from === null) {
			$use_case->continueScrappingUsers($limit);
			return;
		}
		$use_case->scrapeUsersFromLimit(new FromLimit($from, $limit));
	}
}
