<?php

namespace Test\Gitrub\App\WebRest\Repository\Controller;

use EBANX\Stream\Stream;
use Gitrub\App\Web\Rest\Repository\Controller\ListRepositoryController;
use Gitrub\App\Web\Rest\Repository\Controller\Presenter\RepositoryCollectionPresenter;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Test\Gitrub\Gateway\Repository\MockRepositoryGateway;
use Test\Gitrub\GitrubTestCase;
use Test\Gitrub\Support\Traits\GetGlobalCleaner;

class ListRepositoryControllerTest extends GitrubTestCase {

	use GetGlobalCleaner;

	public function testListRepositoriesWithDefaultParams(): void {
		$repositories = $this->faker->createsABunchOfRepositories(n_repositories: 51);
		$response = (new ListRepositoryController(new MockRepositoryGateway($repositories)))
			->listRepositories()
			->asResponse();

		$expected_response = (new RepositoryCollectionPresenter(
			new RepositoryCollection(Stream::of($repositories)->take(50))
		))->asResponse();
		self::assertEquals(
			$expected_response->asResponse(),
			$response->asResponse()
		);
	}

	public function testListRepositoriesPassingParams(): void {
		$repositories = $this->faker->createsABunchOfRepositories(n_repositories: 12);
		$_GET['from'] = $repositories[0]->id + 1;
		$_GET['limit'] = 10;

		$response = (new ListRepositoryController(new MockRepositoryGateway($repositories)))
			->listRepositories()
			->asResponse();

		$expected_response = (new RepositoryCollectionPresenter(
			new RepositoryCollection(Stream::of($repositories)->skip(1)->take(10))
		))->asResponse();
		self::assertEquals(
			$expected_response,
			$response
		);
	}

	public function testListForkRepositoriesWithDefaultParams(): void {
		$repositories = $this->faker->createsABunchOfRepositories(n_repositories: 51, fork: true);
		$response = (new ListRepositoryController(new MockRepositoryGateway($repositories)))
			->listForkRepositories()
			->asResponse();

		$expected_response = (new RepositoryCollectionPresenter(
			new RepositoryCollection(Stream::of($repositories)->take(50))
		))->asResponse();
		self::assertEquals(
			$expected_response->asResponse(),
			$response->asResponse()
		);
	}

	public function testListForkRepositoriesPassingParams(): void {
		$non_fork_repositories = $this->faker->createsABunchOfRepositories(n_repositories: 10);
		$fork_repositories = $this->faker->createsABunchOfRepositories(n_repositories: 12, fork: true);
		$_GET['from'] = $fork_repositories[0]->id + 1;
		$_GET['limit'] = 10;

		$response = (new ListRepositoryController(new MockRepositoryGateway(array_merge($non_fork_repositories, $fork_repositories))))
			->listForkRepositories()
			->asResponse();

		$expected_response = (new RepositoryCollectionPresenter(
			new RepositoryCollection(Stream::of($fork_repositories)->skip(1)->take(10))
		))->asResponse();
		self::assertEquals(
			$expected_response,
			$response
		);
	}

	public function testListRepositoriesFromOwnerWithDefaultParams(): void {
		$owner = $this->faker->user();
		$repositories = $this->faker->createsABunchOfRepositories(n_repositories: 51, owner: $owner);
		$response = (new ListRepositoryController(new MockRepositoryGateway($repositories)))
			->listRepositoriesFromOwner($owner->id)
			->asResponse();

		$expected_response = (new RepositoryCollectionPresenter(
			new RepositoryCollection(Stream::of($repositories)->take(50))
		))->asResponse();
		self::assertEquals(
			$expected_response->asResponse(),
			$response->asResponse()
		);
	}

	public function testListRepositoriesFromOwnerPassingParams(): void {
		$repositories_other_owners = $this->faker->createsABunchOfRepositories(n_repositories: 10);
		$owner = $this->faker->user();
		$repositories_from_owner = $this->faker->createsABunchOfRepositories(n_repositories: 12, owner: $owner);
		$_GET['from'] = $repositories_from_owner[0]->id + 1;
		$_GET['limit'] = 10;

		$response = (new ListRepositoryController(new MockRepositoryGateway(
			array_merge($repositories_other_owners, $repositories_from_owner)
		)))
			->listRepositoriesFromOwner($owner->id)
			->asResponse();

		$expected_response = (new RepositoryCollectionPresenter(
			new RepositoryCollection(Stream::of($repositories_from_owner)->skip(1)->take(10))
		))->asResponse();
		self::assertEquals(
			$expected_response,
			$response
		);
	}

	public function testListRepositoriesWithNameWithDefaultParams(): void {
		$repositories = $this->faker->createsABunchOfRepositories(n_repositories: 51, name: 'name');
		$response = (new ListRepositoryController(new MockRepositoryGateway($repositories)))
			->listRepositoriesWithName('name')
			->asResponse();

		$expected_response = (new RepositoryCollectionPresenter(
			new RepositoryCollection(Stream::of($repositories)->take(50))
		))->asResponse();
		self::assertEquals(
			$expected_response->asResponse(),
			$response->asResponse()
		);
	}

	public function testListRepositoriesWithNamePassingParams(): void {
		$repositories_other_names = $this->faker->createsABunchOfRepositories(n_repositories: 10);
		$repositories_with_name = $this->faker->createsABunchOfRepositories(n_repositories: 51, name: 'name');
		$_GET['from'] = $repositories_with_name[0]->id + 1;
		$_GET['limit'] = 10;

		$response = (new ListRepositoryController(new MockRepositoryGateway(
			array_merge($repositories_other_names, $repositories_with_name)
		)))
			->listRepositoriesWithName('name')
			->asResponse();

		$expected_response = (new RepositoryCollectionPresenter(
			new RepositoryCollection(Stream::of($repositories_with_name)->skip(1)->take(10))
		))->asResponse();
		self::assertEquals(
			$expected_response,
			$response
		);
	}
}
