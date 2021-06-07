<?php


namespace Test\Gitrub\Support\Traits;


use Gitrub\App\GatewayInstances;
use Gitrub\App\Web\Response\ResponseHandler;
use Gitrub\App\Web\WebApp;
use Gitrub\Domain\Repository\Gateway\RepositoryGithubGateway;
use Gitrub\Domain\User\Gateway\UserGithubGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryGithubGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryScrapeStateGateway;
use Test\Gitrub\Gateway\User\MockUserGateway;
use Test\Gitrub\Gateway\User\MockUserGithubGateway;
use Test\Gitrub\Gateway\User\MockUserScrapeStateGateway;

trait FakeRequest {

    use ServerGlobalCleaner;
    use GetGlobalCleaner;

    private function fakeRequest(WebApp $web_app, string $uri, string $method = 'GET'): void {
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REQUEST_METHOD'] = $method;
        $web_app->run();
    }

    private function buildWebApp(
        ResponseHandler $response_handler,
        array $users = [],
        array $repositories = [],
        RepositoryGithubGateway $repository_github_gateway = null,
        UserGithubGateway $user_github_gateway = null,
    ): WebApp {
        return new WebApp(
           new GatewayInstances(
               user_gateway: new MockUserGateway($users),
               user_github_gateway: $user_github_gateway ?? new MockUserGithubGateway([]),
               user_scrape_state_gateway: new MockUserScrapeStateGateway(),
               repository_gateway: new MockRepositoryGateway($repositories),
               repository_github_gateway: $repository_github_gateway ?? new MockRepositoryGithubGateway([]),
               repository_scrape_state_gateway: new MockRepositoryScrapeStateGateway(),
           ),
            $response_handler,
        );
    }
}
