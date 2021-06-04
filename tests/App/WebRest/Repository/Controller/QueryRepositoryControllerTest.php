<?php

namespace Test\Gitrub\App\WebRest\Repository\Controller;

use Gitrub\App\Web\Rest\Repository\Controller\Presenter\RepositoryPresenter;
use Gitrub\App\Web\Rest\Repository\Controller\QueryRepositoryController;
use Gitrub\App\Web\Rest\Response\Response;
use Test\Gitrub\Gateway\Repository\MockRepositoryGateway;
use Test\Gitrub\GitrubTestCase;

class QueryRepositoryControllerTest extends GitrubTestCase {

	public function testGetRepositoryByIdNotFound(): void {
		self::assertEquals(
			new Response(httpCode: 404, body: '{"error":"Repository not found with id -1"}'),
			(new QueryRepositoryController(new MockRepositoryGateway([])))
				->getRepositoryById(-1)->asResponse()
		);
	}

	public function testGetRepositoryById(): void {
		$repository = $this->faker->repository();
		self::assertEquals(
			(new RepositoryPresenter($repository))->asResponse(),
			(new QueryRepositoryController(new MockRepositoryGateway([$repository])))
				->getRepositoryById($repository->id)->asResponse()
		);
	}

	public function testGetRepositoryByFullNameNotFound(): void {
		self::assertEquals(
			new Response(httpCode: 404, body: '{"error":"Repository not found with full name invalid\/repository"}'),
			(new QueryRepositoryController(new MockRepositoryGateway([])))
				->getRepositoryByFullName('invalid/repository')->asResponse()
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
