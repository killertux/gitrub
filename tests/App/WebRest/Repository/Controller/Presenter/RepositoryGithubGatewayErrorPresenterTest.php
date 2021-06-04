<?php

namespace Test\Gitrub\App\WebRest\Repository\Controller\Presenter;

use Gitrub\App\Web\Rest\Repository\Controller\Presenter\RepositoryGithubGatewayErrorPresenter;
use Gitrub\App\Web\Rest\Response\Response;
use Gitrub\Domain\Repository\Exception\RepositoryGithubGatewayError;
use Test\Gitrub\GitrubTestCase;

class RepositoryGithubGatewayErrorPresenterTest extends GitrubTestCase {

	public function testAsResponse(): void {
		self::assertEquals(
			new Response(
				httpCode: 500,
				body: '{"error":"Internal github error"}'
			),
			(new RepositoryGithubGatewayErrorPresenter(
				new RepositoryGithubGatewayError('Internal github error')
			))->asResponse()
		);
	}
}
