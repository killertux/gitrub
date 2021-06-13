<?php


namespace Gitrub\App\Web\GraphQL\Resolver\Repository;


use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryGithubGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryScrapeStateGateway;
use Gitrub\Domain\Repository\UseCase\ScrapeRepositoryUseCase;

class ScrapeRepositoryResolver
{

    public function __construct(
        private RepositoryGateway $repository_gateway,
        private RepositoryGithubGateway $repository_github_gateway,
        private RepositoryScrapeStateGateway $repository_scrape_state_gateway)
    {}

    public function scrapeRepositories(array $args): string {
        (new ScrapeRepositoryUseCase(
            $this->repository_gateway,
            $this->repository_github_gateway,
            $this->repository_scrape_state_gateway
        ))->scrapeRepositoriesFromLimit(new FromLimit($args['from_limit']['from'], $args['from_limit']['limit']));
        return 'Repositories scrapped!';
    }

    public function continueToScrapeRepositories(array $args): string {
        (new ScrapeRepositoryUseCase(
            $this->repository_gateway,
            $this->repository_github_gateway,
            $this->repository_scrape_state_gateway
        ))->continueScrappingRepositories($args['limit']);
        return 'Repositories scrapped!';
    }
}
