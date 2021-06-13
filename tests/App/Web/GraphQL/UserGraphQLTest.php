<?php


namespace Test\Gitrub\App\Web\GraphQL;


use EBANX\Stream\Stream;
use Gitrub\Domain\User\Entity\User;
use Test\Gitrub\App\Web\Response\MockResponseHandler;
use Test\Gitrub\Gateway\User\MockUserGateway;
use Test\Gitrub\Gateway\User\MockUserScrapeStateGateway;
use Test\Gitrub\GitrubTestCase;
use Test\Gitrub\Support\Traits\FakeRequest;

class UserGraphQLTest extends GitrubTestCase {

    use FakeRequest;

    public function testQueryUser(): void {
        $users = [$this->faker->user(), $this->faker->user()];
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            users: $users
        );

        $query = "query {user(id:{$users[0]->id}) {id, login}}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertEquals($users[0]->id, $response->data->user->id);
        self::assertEquals($users[0]->login, $response->data->user->login);
    }

    public function testQueryUserByLogin(): void {
        $users = [$this->faker->user(), $this->faker->user()];
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            users: $users
        );

        $query = "query {userByLogin(login:\"{$users[0]->login}\") {id, login}}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertEquals($users[0]->id, $response->data->userByLogin->id);
        self::assertEquals($users[0]->login, $response->data->userByLogin->login);
    }

    public function testQueryUsers(): void {
        $users = $this->faker->createsABunchOfUsers(10);
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            users: $users
        );

        $query = "query {users(from_limit: {from: {$users[3]->id}, limit: 5}) {id, login}}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertCount(5, $response->data->users);
        self::assertEquals(
            Stream::of($users)
                ->skip(3)
                ->take(5)
                ->map(fn(User $user) => (object)['id' => $user->id, 'login' => $user->login])
                ->collect(),
            $response->data->users
        );
    }

    public function testQueryAdmins(): void {
        $users = $this->faker->createsABunchOfAdminUsers(10);
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            users: $users
        );

        $query = "query {admins(from_limit: {from: {$users[3]->id}, limit: 5}) {id, login}}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertCount(5, $response->data->admins);
        self::assertEquals(
            Stream::of($users)
                ->skip(3)
                ->take(5)
                ->map(fn(User $user) => (object)['id' => $user->id, 'login' => $user->login])
                ->collect(),
            $response->data->admins
        );
    }

    public function testQueryAdminsWithoutAnyAdmin(): void {
        $users = [$this->faker->user()];
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            users: $users
        );

        $query = "query {admins(from_limit: {from: {$users[0]->id}, limit: 5}) {id, login}}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertCount(0, $response->data->admins);
    }

    public function testQueryRepositoriesFromUSer(): void {
        $user = $this->faker->user();
        $repositories = [$this->faker->repository(owner: $user), $this->faker->repository(owner: $user), $this->faker->repository()];

        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            users: [$user],
            repositories: $repositories,
        );

        $query = "query {user(id: $user->id) {id, repositories {id, full_name}}}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertEquals($user->id, $response->data->user->id);
        self::assertEquals([
            (object)['id' => $repositories[0]->id, 'full_name' => $repositories[0]->full_name],
            (object)['id' => $repositories[1]->id, 'full_name' => $repositories[1]->full_name],
        ], $response->data->user->repositories);
    }

    public function testMutationScrapeRepositories(): void {
        $users = $this->faker->createsABunchOfUsers(10);
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            github_users: $users,
            user_gateway: $user_gateway = new MockUserGateway([]),
        );

        $query = "mutation {scrapeUsers(from_limit: {from: {$users[3]->id}, limit: 5})}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertEquals('Users scrapped!', $response->data->scrapeUsers);
        $user_gateway->assertSavedUsers(
            Stream::of($users)
                ->skip(3)
                ->take(5)
                ->collect()
        );
    }

    public function testMutationContinueToScrapeRepositories(): void {
        $users = $this->faker->createsABunchOfUsers(10);
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            github_users: $users,
            user_gateway: $user_gateway = new MockUserGateway([]),
            user_scrape_state_gateway: new MockUserScrapeStateGateway($users[2]->id)
        );

        $query = "mutation {continueToScrapeUsers(limit: 6)}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertEquals('Users scrapped!', $response->data->continueToScrapeUsers);
        $user_gateway->assertSavedUsers(
            Stream::of($users)
                ->skip(3)
                ->take(6)
                ->collect()
        );
    }
}
