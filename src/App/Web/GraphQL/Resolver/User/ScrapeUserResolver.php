<?php


namespace Gitrub\App\Web\GraphQL\Resolver\User;


use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Domain\User\Gateway\UserGithubGateway;
use Gitrub\Domain\User\Gateway\UserScrapeStateGateway;
use Gitrub\Domain\User\UseCase\ScrapeUserUseCase;

class ScrapeUserResolver
{
    public function __construct(
        private UserGateway $repository_gateway,
        private UserGithubGateway $repository_github_gateway,
        private UserScrapeStateGateway $repository_scrape_state_gateway
    ) {}

    public function scrapeUsers(array $args): string {
        (new ScrapeUserUseCase(
            $this->repository_gateway,
            $this->repository_github_gateway,
            $this->repository_scrape_state_gateway
        ))->scrapeUsersFromLimit(new FromLimit($args['from_limit']['from'], $args['from_limit']['limit']));
        return 'Users scrapped!';
    }

    public function continueToScrapeUsers(array $args): string {
        (new ScrapeUserUseCase(
            $this->repository_gateway,
            $this->repository_github_gateway,
            $this->repository_scrape_state_gateway
        ))->continueScrappingUsers($args['limit']);
        return 'Users scrapped!';
    }
}
