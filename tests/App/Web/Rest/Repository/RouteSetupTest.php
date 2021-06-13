<?php

namespace Test\Gitrub\App\Web\Rest\Repository;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Gitrub\Domain\Repository\Exception\RepositoryGithubGatewayError;
use Gitrub\Domain\Repository\Gateway\RepositoryGithubGateway;
use Test\Gitrub\App\Web\Response\MockResponseHandler;
use Test\Gitrub\GitrubTestCase;
use Test\Gitrub\Support\Traits\FakeRequest;

class RouteSetupTest extends GitrubTestCase {

    use FakeRequest;

    public function testGetRepositoryByIdNotFound(): void {
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler);
        $this->fakeRequest($web_app, '/repository/42');

        self::assertEquals(404, $response_handler->last_response->http_code);
        self::assertEquals('{"error":"Repository not found with id 42"}', $response_handler->last_response->body);
    }

    public function testGetRepositoryByFullNameNotFound(): void {
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler);
        $this->fakeRequest($web_app, '/repositoryWithFullName/invalid/repository');

        self::assertEquals(404, $response_handler->last_response->http_code);
        self::assertEquals('{"error":"Repository not found with full name invalid\/repository"}', $response_handler->last_response->body);
    }

    public function testPassingNegativeLimitToScrape(): void {
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler);
        $this->fakeRequest($web_app, '/repository/scrape', 'POST', ['limit' => -1]);

        self::assertEquals(400, $response_handler->last_response->http_code);
        self::assertEquals('{"error":"Limit must be a positive number"}', $response_handler->last_response->body);
    }

    public function testErrorInGithub(): void {
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler, repository_github_gateway: $this->createFailingGithubGateway());
        $this->fakeRequest($web_app, '/repository/scrape', 'POST');

        self::assertEquals(500, $response_handler->last_response->http_code);
        self::assertEquals('{"error":"Internal github error"}', $response_handler->last_response->body);
    }

    private function createFailingGithubGateway(): RepositoryGithubGateway {
        return new class implements RepositoryGithubGateway {
            public function listRepositories(FromLimit $from_limit): RepositoryCollection {
                throw new RepositoryGithubGatewayError('Internal github error');
            }
        };
    }
}
