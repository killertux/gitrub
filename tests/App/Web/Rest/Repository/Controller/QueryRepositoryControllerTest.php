<?php

namespace Test\Gitrub\App\Web\Rest\Repository\Controller;

use Gitrub\App\Web\Request\Request;
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
				->getRepositoryById(Request::empty(), $repository->id)->asResponse()
		);
	}

	public function testGetRepositoryByFullName(): void {
		$repository = $this->faker->repository();
		self::assertEquals(
			(new RepositoryPresenter($repository))->asResponse(),
			(new QueryRepositoryController(new MockRepositoryGateway([$repository])))
				->getRepositoryByFullName(Request::empty(), $repository->full_name)->asResponse()
		);
	}
}
