<?php


namespace Test\Gitrub\App\Web\GraphQL;


use EBANX\Stream\Stream;
use Gitrub\Domain\Repository\Entity\Repository;
use Test\Gitrub\App\Web\Response\MockResponseHandler;
use Test\Gitrub\Gateway\Repository\MockRepositoryGateway;
use Test\Gitrub\Gateway\Repository\MockRepositoryScrapeStateGateway;
use Test\Gitrub\GitrubTestCase;
use Test\Gitrub\Support\Traits\FakeRequest;

class RepositoryGraphQLTest extends GitrubTestCase {

    use FakeRequest;

    public function testQueryRepository(): void {
        $repositories = [$this->faker->repository(), $this->faker->repository()];
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            repositories: $repositories
        );

        $query = "query {repository(id:{$repositories[0]->id}) {id, full_name, owner {id}}}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertEquals($repositories[0]->id, $response->data->repository->id);
        self::assertEquals($repositories[0]->full_name, $response->data->repository->full_name);
        self::assertEquals($repositories[0]->owner->id, $response->data->repository->owner->id);
    }

    public function testQueryRepositoryByFullName(): void {
        $repositories = [$this->faker->repository(), $this->faker->repository()];
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            repositories: $repositories
        );

        $query = "query {repositoryByFullName(full_name:\"{$repositories[0]->full_name}\") {id, full_name}}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertEquals($repositories[0]->id, $response->data->repositoryByFullName->id);
        self::assertEquals($repositories[0]->full_name, $response->data->repositoryByFullName->full_name);
    }

    public function testQueryRepositories(): void {
        $repositories = $this->faker->createsABunchOfRepositories(10);
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            repositories: $repositories
        );

        $query = "query {repositories(from_limit: {from: {$repositories[3]->id}, limit: 5}) {id, full_name}}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertCount(5, $response->data->repositories);
        self::assertEquals(
            Stream::of($repositories)
                ->skip(3)
                ->take(5)
                ->map(fn(Repository $repository) => (object)['id' => $repository->id, 'full_name' => $repository->full_name])
                ->collect(),
            $response->data->repositories
        );
    }

    public function testQueryRepositoriesWithName(): void {
        $repositories = $this->faker->createsABunchOfRepositories(10, name: 'test');
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            repositories: $repositories
        );

        $query = "query {repositoriesWithName(from_limit: {from: {$repositories[3]->id}, limit: 5}, name: \"test\") {id, full_name}}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertCount(5, $response->data->repositoriesWithName);
        self::assertEquals(
            Stream::of($repositories)
                ->skip(3)
                ->take(5)
                ->map(fn(Repository $repository) => (object)['id' => $repository->id, 'full_name' => $repository->full_name])
                ->collect(),
            $response->data->repositoriesWithName
        );
    }

    public function testQueryForks(): void {
        $repositories = $this->faker->createsABunchOfRepositories(10, fork: true);
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            repositories: $repositories
        );

        $query = "query {forks(from_limit: {from: {$repositories[3]->id}, limit: 5}) {id, full_name}}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertCount(5, $response->data->forks);
        self::assertEquals(
            Stream::of($repositories)
                ->skip(3)
                ->take(5)
                ->map(fn(Repository $repository) => (object)['id' => $repository->id, 'full_name' => $repository->full_name])
                ->collect(),
            $response->data->forks
        );
    }

    public function testMutationScrapeRepositories(): void {
        $repositories = $this->faker->createsABunchOfRepositories(10);
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            github_repositories: $repositories,
            repository_gateway: $repository_gateway = new MockRepositoryGateway([]),
        );

        $query = "mutation {scrapeRepositories(from_limit: {from: {$repositories[3]->id}, limit: 5})}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertEquals('Repositories scrapped!', $response->data->scrapeRepositories);
        $repository_gateway->assertSavedRepositories(
            Stream::of($repositories)
            ->skip(3)
            ->take(5)
            ->collect()
        );
    }

    public function testMutationContinueToScrapeRepositories(): void {
        $repositories = $this->faker->createsABunchOfRepositories(10);
        $response_handler = new MockResponseHandler();
        $web_app = $this->buildWebApp($response_handler,
            github_repositories: $repositories,
            repository_gateway: $repository_gateway = new MockRepositoryGateway([]),
            repository_scrape_state_gateway: new MockRepositoryScrapeStateGateway($repositories[1]->id)
        );

        $query = "mutation {continueToScrapeRepositories(limit: 6)}";
        $this->fakeGraphQLRequest($web_app, $query);
        $response = json_decode($response_handler->last_response->body);
        self::assertEquals('Repositories scrapped!', $response->data->continueToScrapeRepositories);
        $repository_gateway->assertSavedRepositories(
            Stream::of($repositories)
                ->skip(2)
                ->take(6)
                ->collect()
        );
    }
}
