<?php

namespace Test\Gitrub\App\Web\Rest\User\Controller\Presenter;

use Gitrub\App\Web\Response\Response;
use Gitrub\App\Web\Rest\User\Controller\Presenter\UserGithubGatewayErrorPresenter;
use Gitrub\Domain\User\Exception\UserGithubGatewayError;
use Test\Gitrub\GitrubTestCase;

class UserGithubGatewayErrorPresenterTest extends GitrubTestCase {

	public function testAsResponse() : void {
		self::assertEquals(
			new Response(
				http_code: 500,
				body: '{"error":"Internal github error"}'
			),
			(new UserGithubGatewayErrorPresenter(
				new UserGithubGatewayError('Internal github error')
			))->asResponse()
		);
	}
}
