<?php

namespace Test\Gitrub\Gateway\Github\RestApi;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Entity\Repository;
use Gitrub\Domain\Repository\Exception\RepositoryGithubGatewayError;
use Gitrub\Domain\User\Entity\User;
use Gitrub\Domain\User\Exception\UserGithubGatewayError;
use Gitrub\Gateway\Github\RestApi\GithubPersonalAccessToken;
use Gitrub\Gateway\Github\RestApi\GithubRestAPIGateway;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Test\Gitrub\GitrubTestCase;

class GithubRestAPIGatewayTest extends GitrubTestCase {

	public function testListUsers(): void {
		$users = [$this->faker->user(), $this->faker->user()];
		$mock_handler = new MockHandler([self::createMockResponseForListUsers(...$users)]);
		$client = new Client(['handler' => $mock_handler]);

		self::assertEquals($users,
			iterator_to_array(
				(new GithubRestAPIGateway($client, GithubPersonalAccessToken::createEmpty()))
					->listUsers(new FromLimit($users[0]->id, 2))
			)
		);
		self::assertFirstUserRequest($mock_handler, $users[0]->id);
	}

	public function testListUsersFromMultiplePages(): void {
		$users_1 = [$this->faker->user(), $this->faker->user()];
		$users_2 = [$this->faker->user(), $this->faker->user()];
		$mock_handler = new MockHandler([
			self::createMockResponseForListUsers(...$users_1),
			self::createMockResponseForListUsers(...$users_2),
		]);
		$client = new Client(['handler' => $mock_handler]);

		self::assertEquals(array_merge($users_1, $users_2),
			iterator_to_array(
				(new GithubRestAPIGateway($client, GithubPersonalAccessToken::createEmpty()))
					->listUsers(new FromLimit($users_1[0]->id, 4))
			)
		);
	}

	public function testListUsersTakingLessThanAvailable(): void {
		$users_1 = [$this->faker->user(), $this->faker->user()];
		$users_2 = [$this->faker->user(), $this->faker->user()];
		$mock_handler = new MockHandler([
			self::createMockResponseForListUsers(...$users_1),
			self::createMockResponseForListUsers(...$users_2),
		]);
		$client = new Client(['handler' => $mock_handler]);

		self::assertEquals(array_merge($users_1, [$users_2[0]]),
			iterator_to_array(
				(new GithubRestAPIGateway($client, GithubPersonalAccessToken::createEmpty()))
					->listUsers(new FromLimit($users_1[0]->id, 3))
			)
		);
	}

	public function testListUsersWithoutNextLink_ShouldStop(): void {
		$users = [$this->faker->user(), $this->faker->user()];
		$mock_handler = new MockHandler([
			self::createMockResponseForListUsers(...$users),
			self::createMockResponseForListUsers(),
		]);
		$client = new Client(['handler' => $mock_handler]);

		self::assertEquals($users,
			iterator_to_array(
				(new GithubRestAPIGateway($client, GithubPersonalAccessToken::createEmpty()))
					->listUsers(new FromLimit($users[0]->id, 4))
			)
		);
	}

	public function testListRepositories(): void {
		$repositories = [$this->faker->repository(), $this->faker->repository()];
		$mock_handler = new MockHandler([self::createMockResponseForListRepositories(...$repositories)]);
		$client = new Client(['handler' => $mock_handler]);

		self::assertEquals($repositories,
			iterator_to_array(
				(new GithubRestAPIGateway($client, GithubPersonalAccessToken::createEmpty()))
					->listRepositories(new FromLimit($repositories[0]->id, 2))
			)
		);
		self::assertFirstRepositoryRequest($mock_handler, $repositories[0]->id);
	}

	public function testRepositoriesUsersFromMultiplePages(): void {
		$repositories_1 = [$this->faker->repository(), $this->faker->repository()];
		$repositories_2 = [$this->faker->repository(), $this->faker->repository()];
		$mock_handler = new MockHandler([
			self::createMockResponseForListRepositories(...$repositories_1),
			self::createMockResponseForListRepositories(...$repositories_2),
		]);
		$client = new Client(['handler' => $mock_handler]);

		self::assertEquals(array_merge($repositories_1, $repositories_2),
			iterator_to_array(
				(new GithubRestAPIGateway($client, GithubPersonalAccessToken::createEmpty()))
					->listRepositories(new FromLimit($repositories_1[0]->id, 4))
			)
		);
	}

	public function testListRepositoriesTakingLessThanAvailable(): void {
		$repositories_1 = [$this->faker->repository(), $this->faker->repository()];
		$repositories_2 = [$this->faker->repository(), $this->faker->repository()];
		$mock_handler = new MockHandler([
			self::createMockResponseForListRepositories(...$repositories_1),
			self::createMockResponseForListRepositories(...$repositories_2),
		]);
		$client = new Client(['handler' => $mock_handler]);

		self::assertEquals(array_merge($repositories_1, [$repositories_2[0]]),
			iterator_to_array(
				(new GithubRestAPIGateway($client, GithubPersonalAccessToken::createEmpty()))
					->listRepositories(new FromLimit($repositories_1[0]->id, 3))
			)
		);
	}

	public function testListRepositoriesWithoutNextLink_ShouldStop(): void {
		$repositories = [$this->faker->repository(), $this->faker->repository()];
		$mock_handler = new MockHandler([
			self::createMockResponseForListRepositories(...$repositories),
			self::createMockResponseForListRepositories(),
		]);
		$client = new Client(['handler' => $mock_handler]);

		self::assertEquals($repositories,
			iterator_to_array(
				(new GithubRestAPIGateway($client, GithubPersonalAccessToken::createEmpty()))
					->listRepositories(new FromLimit($repositories[0]->id, 4))
			)
		);
	}

	public function testListRepositoriesReturningErro403_ShouldWrapError(): void {
		$this->expectException(RepositoryGithubGatewayError::class);
		$mock_handler = MockHandler::createWithMiddleware([
			new Response(403, [], '{"message":"API rate limit exceeded for 18.231.41.151. (But here\'s the good news: Authenticated requests get a higher rate limit)"}')
		]);
		$client = new Client(['handler' => $mock_handler]);
		iterator_to_array(
			(new GithubRestAPIGateway($client, GithubPersonalAccessToken::createEmpty()))
				->listRepositories(new FromLimit(0, 40))
		);
	}

	public function testListUsersReturningErro403_ShouldWrapError(): void {
		$this->expectException(UserGithubGatewayError::class);
		$mock_handler = MockHandler::createWithMiddleware([
			new Response(403, [], '{"message":"API rate limit exceeded for 18.231.41.151. (But here\'s the good news: Authenticated requests get a higher rate limit)"}')
		]);
		$client = new Client(['handler' => $mock_handler]);
		iterator_to_array(
			(new GithubRestAPIGateway($client, GithubPersonalAccessToken::createEmpty()))
				->listUsers(new FromLimit(0, 40))
		);
	}

	public function testListRepositoriesWithPersonalAccessToken_ShouldUseToken(): void {
		$repositories = [$this->faker->repository(), $this->faker->repository()];
		$mock_handler = new MockHandler([self::createMockResponseForListRepositories(...$repositories)]);
		$client = new Client(['handler' => $mock_handler]);

		self::assertEquals($repositories,
			iterator_to_array(
				(new GithubRestAPIGateway($client, new GithubPersonalAccessToken('some-username', 'some-token')))
					->listRepositories(new FromLimit($repositories[0]->id, 2))
			)
		);
		self::assertFirstRepositoryRequestWithAccessToken($mock_handler, $repositories[0]->id);
	}

	private static function createMockResponseForListUsers(User ...$users): Response {
		if (empty($users)) {
			return new Response(
				status: 200,
				headers: ["<https://api.github.com/users{?since}>; rel=\"first\""],
				body: json_encode($users)
			);
		}
		$next_batch_user_id = $users[count($users) - 1]->id + 1;
		return new Response(
			status: 200,
			headers: ["Link" => "<https://api.github.com/users?since=$next_batch_user_id&limit=100>; rel=\"next\", <https://api.github.com/users{?since}>; rel=\"first\""],
			body: json_encode($users)
		);
	}

	private static function createMockResponseForListRepositories(Repository ...$repositories): Response {
		if (empty($repositories)) {
			return new Response(
				status: 200,
				headers: ["<https://api.github.com/repositories{?since}>; rel=\"first\""],
				body: json_encode($repositories)
			);
		}
		$next_batch_user_id = $repositories[count($repositories) - 1]->id + 1;
		return new Response(
			status: 200,
			headers: ["Link" => "<https://api.github.com/repositories?since=$next_batch_user_id>; rel=\"next\", <https://api.github.com/users{?since}>; rel=\"first\""],
			body: json_encode($repositories)
		);
	}

	private static function assertFirstUserRequest(MockHandler $mock_handler, int $first_user_id): void {
		$last_request = $mock_handler->getLastRequest();
		self::assertEquals(Uri::withQueryValues(new Uri('https://api.github.com/users'), [
			'since' => $first_user_id,
			'limit' => 100
		]), $last_request->getUri());
		self::assertEquals(['application/vnd.github.v3+json'], $last_request->getHeader('Accept'));
	}

	private static function assertFirstRepositoryRequest(MockHandler $mock_handler, int $first_repository_id): void {
		$last_request = $mock_handler->getLastRequest();
		self::assertEquals(Uri::withQueryValues(new Uri('https://api.github.com/repositories'), [
			'since' => $first_repository_id,
		]), $last_request->getUri());
		self::assertEquals(['application/vnd.github.v3+json'], $last_request->getHeader('Accept'));
	}

	private static function assertFirstRepositoryRequestWithAccessToken(MockHandler $mock_handler, int $first_repository_id): void {
		self::assertFirstRepositoryRequest($mock_handler, $first_repository_id);
		$basic_auth = base64_encode('some-username:some-token');
		self::assertEquals(['Basic ' . $basic_auth], $mock_handler->getLastRequest()->getHeader('Authorization'));
	}
}
