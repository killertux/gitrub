<?php

namespace Test\Gitrub\App\WebRest\Repository\Controller\Presenter;

use Gitrub\App\Web\Rest\Repository\Controller\Presenter\RepositoryNotFoundPresenter;
use Gitrub\App\Web\Rest\Response\Response;
use Gitrub\Domain\Repository\Exception\RepositoryNotFound;
use Test\Gitrub\GitrubTestCase;

class RepositoryNotFoundPresenterTest extends GitrubTestCase {

	public function testAsResponse(): void {
		self::assertEquals(
			new Response(httpCode: 404, body: '{"error":"Repository not found with full name invalid-repository"}'),
			(new RepositoryNotFoundPresenter(new RepositoryNotFound('Repository not found with full name invalid-repository')))
				->asResponse()
		);
	}
}