<?php

namespace Test\Gitrub\App\Web\Rest\Repository\Controller;

use Gitrub\App\Web\Rest\Repository\Controller\Presenter\RepositoryPresenter;
use Gitrub\App\Web\Rest\Repository\Controller\QueryRepositoryController;
use Test\Gitrub\Gateway\Repository\MockRepositoryGateway;
use Test\Gitrub\GitrubTestCase;

class QueryRepositoryControllerTest extends GitrubTestCase {

	public function testGetRepositoryById(): void {
		$repository = $this->faker->repository();
		self::assertEquals(
			(new RepositoryPresenter($repository))->asResponse(),
			(new QueryRepositoryController(new MockRepositoryGateway([$repository])))
				->getRepositoryById($repository->id)->asResponse()
		);
	}

	public function testGetRepositoryByFullName(): void {
		$repository = $this->faker->repository();
		self::assertEquals(
			(new RepositoryPresenter($repository))->asResponse(),
			(new QueryRepositoryController(new MockRepositoryGateway([$repository])))
				->getRepositoryByFullName($repository->full_name)->asResponse()
		);
	}
}
