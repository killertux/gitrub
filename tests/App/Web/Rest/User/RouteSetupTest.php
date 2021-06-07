<?php

namespace Test\Gitrub\App\Web\Rest\User;

use Gitrub\App\Web\Rest\User\RouteSetup;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Exception\UserGithubGatewayError;
use Gitrub\Domain\User\Gateway\UserGithubGateway;
use PHPUnit\Framework\TestCase;
use Test\Gitrub\App\Web\Response\MockResponseHandler;
use Test\Gitrub\GitrubTestCase;
use Test\Gitrub\Support\Traits\FakeRequest;
use Test\Gitrub\Support\Traits\GetGlobalCleaner;

class RouteSetupTest extends GitrubTestCase {
    use FakeRequest;
    use GetGlobalCleaner;

    public function testQueryUserByIdNotFound(): void {
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler);

        $this->fakeRequest($web_app, '/user/42');
        self::assertEquals(404, $response_handler->last_response->http_code);
        self::assertEquals('{"error":"User not found with id 42"}', $response_handler->last_response->body);
    }

    public function testGetUserByLoginNotFound(): void {
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler);

        $this->fakeRequest($web_app, '/user/login/invalid-login');
        self::assertEquals(404, $response_handler->last_response->http_code);
        self::assertEquals('{"error":"User not found with login invalid-login"}', $response_handler->last_response->body);
    }

    public function testPassingNegativeLimitToScrape(): void {
        $_GET['limit'] = -1;
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler);

        $this->fakeRequest($web_app, '/user/scrape', 'POST');
        self::assertEquals(400, $response_handler->last_response->http_code);
        self::assertEquals('{"error":"Limit must be a positive number"}', $response_handler->last_response->body);
    }

    public function testErrorInGithub(): void {
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler, user_github_gateway: $this->createFailingGithubGateway());

        $this->fakeRequest($web_app, '/user/scrape', 'POST');

        self::assertEquals(500, $response_handler->last_response->http_code);
        self::assertEquals('{"error":"Internal github error"}', $response_handler->last_response->body);
    }

    private function createFailingGithubGateway(): UserGithubGateway {
        return new class implements UserGithubGateway {
            public function listUsers(FromLimit $from_limit): UserCollection {
                throw new UserGithubGatewayError('Internal github error');
            }
        };
    }
}
