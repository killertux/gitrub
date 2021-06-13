<?php


namespace Test\Gitrub\Support\Traits;


use Gitrub\App\GatewayInstances;
use Gitrub\App\Web\Request\Request;
use Gitrub\App\Web\Response\ResponseHandler;
use Gitrub\App\Web\WebApp;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryGithubGateway;
use Gitrub\Domain\Repository\Gateway\RepositoryScrapeStateGateway;
use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Domain\User\Gateway\UserGithubGateway;
use Gitrub\Domain\User\Gateway\UserScrapeStateGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryGithubGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryScrapeStateGateway;
use Test\Gitrub\Gateway\User\MockUserGateway;
use Test\Gitrub\Gateway\User\MockUserGithubGateway;
use Test\Gitrub\Gateway\User\MockUserScrapeStateGateway;

trait FakeRequest {

    /**
     * @after
     */
    public function cleanServerGlobals(): void {
        $_SERVER['REQUEST_URI'] = null;
        $_SERVER['REQUEST_METHOD'] = null;
    }

    private function fakeRequest(WebApp $web_app, string $uri, string $method = 'GET', array $query = [], string $body = null): void {
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REQUEST_METHOD'] = $method;
        $web_app->run(new Request($query, $body));
    }

    private function fakeGraphQLRequest(WebApp $web_app, string $query): void {
        $this->fakeRequest(
            $web_app,
            '/graphql',
            'POST',
            body: json_encode(['query' => $query])
        );
    }

    private function buildWebApp(
        ResponseHandler $response_handler,
        array $users = [],
        array $repositories = [],
        array $github_repositories= [],
        array $github_users= [],
        UserGithubGateway $user_github_gateway = null,
        UserGateway $user_gateway = null,
        UserScrapeStateGateway $user_scrape_state_gateway = null,
        RepositoryGithubGateway $repository_github_gateway = null,
        RepositoryGateway $repository_gateway = null,
        RepositoryScrapeStateGateway $repository_scrape_state_gateway = null,
    ): WebApp {
        return new WebApp(
           new GatewayInstances(
               user_gateway:$user_gateway ?? new MockUserGateway($users),
               user_github_gateway: $user_github_gateway ?? new MockUserGithubGateway($github_users),
               user_scrape_state_gateway: $user_scrape_state_gateway ?? new MockUserScrapeStateGateway(),
               repository_gateway: $repository_gateway ?? new MockRepositoryGateway($repositories),
               repository_github_gateway: $repository_github_gateway ?? new MockRepositoryGithubGateway($github_repositories),
               repository_scrape_state_gateway: $repository_scrape_state_gateway ?? new MockRepositoryScrapeStateGateway(),
           ),
            $response_handler,
        );
    }
}
