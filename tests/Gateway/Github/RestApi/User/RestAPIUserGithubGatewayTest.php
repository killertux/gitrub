<?php

namespace Test\Gitrub\Gateway\Github\RestApi\User;

use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Entity\User;
use Gitrub\Domain\User\Exception\UserGithubGatewayError;
use Gitrub\Gateway\Github\RestApi\GithubPersonalAccessToken;
use Gitrub\Gateway\Github\RestApi\User\RestAPIUserGithubGateway;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Test\Gitrub\GitrubTestCase;

class RestAPIUserGithubGatewayTest extends GitrubTestCase {

	public function testListUsers(): void {
		$users = [$this->faker->user(), $this->faker->user()];
		$mock_handler = new MockHandler([self::createMockResponseForListUsers(...$users)]);
		$client = new Client(['handler' => $mock_handler]);

		self::assertEquals($users,
			iterator_to_array(
				(new RestAPIUserGithubGateway($client, GithubPersonalAccessToken::createEmpty()))
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
				(new RestAPIUserGithubGateway($client, GithubPersonalAccessToken::createEmpty()))
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
				(new RestAPIUserGithubGateway($client, GithubPersonalAccessToken::createEmpty()))
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
				(new RestAPIUserGithubGateway($client, GithubPersonalAccessToken::createEmpty()))
					->listUsers(new FromLimit($users[0]->id, 4))
			)
		);
	}

	public function testListUsersReturningError403_ShouldWrapError(): void {
		$this->expectException(UserGithubGatewayError::class);
		$mock_handler = MockHandler::createWithMiddleware([
			new Response(403, [], '{"message":"API rate limit exceeded for 18.231.41.151. (But here\'s the good news: Authenticated requests get a higher rate limit)"}')
		]);
		$client = new Client(['handler' => $mock_handler]);
		iterator_to_array(
			(new RestAPIUserGithubGateway($client, GithubPersonalAccessToken::createEmpty()))
				->listUsers(new FromLimit(0, 40))
		);
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

	private static function assertFirstUserRequest(MockHandler $mock_handler, int $first_user_id): void {
		$last_request = $mock_handler->getLastRequest();
		self::assertEquals(Uri::withQueryValues(new Uri('https://api.github.com/users'), [
			'since' => $first_user_id,
			'limit' => 100
		]), $last_request->getUri());
		self::assertEquals(['application/vnd.github.v3+json'], $last_request->getHeader('Accept'));
	}
}
