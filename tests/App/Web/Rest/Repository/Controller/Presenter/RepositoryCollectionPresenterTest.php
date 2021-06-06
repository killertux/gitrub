<?php

namespace Test\Gitrub\App\Web\Rest\Repository\Controller\Presenter;

use Gitrub\App\Web\Rest\Repository\Controller\Presenter\RepositoryCollectionPresenter;
use Gitrub\App\Web\Response\Response;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Test\Gitrub\GitrubTestCase;

class RepositoryCollectionPresenterTest extends GitrubTestCase {

	public function testPresentEmptyCollection(): void {
		self::assertEquals(
			new Response(httpCode: 200, body: '[]'),
			(new RepositoryCollectionPresenter(
				new RepositoryCollection(
					new \ArrayIterator([])
				)
			))->asResponse()
		);
	}

	public function testPresentCollection(): void {
		$repositories = [$this->faker->repository(), $this->faker->repository()];
		self::assertEquals(
			new Response(httpCode: 200, body: json_encode($repositories)),
			(new RepositoryCollectionPresenter(
				new RepositoryCollection(
					new \ArrayIterator($repositories)
				)
			))->asResponse()
		);
	}
}
