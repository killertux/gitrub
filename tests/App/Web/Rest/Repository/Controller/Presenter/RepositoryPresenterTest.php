<?php

namespace Test\Gitrub\App\Web\Rest\Repository\Controller\Presenter;

use Gitrub\App\Web\Rest\Repository\Controller\Presenter\RepositoryPresenter;
use Gitrub\App\Web\Response\Response;
use Test\Gitrub\GitrubTestCase;

class RepositoryPresenterTest extends GitrubTestCase {

	public function testAsResponse() {
		$repository = $this->faker->repository();
		self::assertEquals(
			new Response(httpCode: 200, body: json_encode($repository)),
			(new RepositoryPresenter($repository))->asResponse()
		);
	}
}
