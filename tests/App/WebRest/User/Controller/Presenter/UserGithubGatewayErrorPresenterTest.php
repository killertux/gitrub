<?php

namespace Test\Gitrub\App\WebRest\User\Controller\Presenter;

use Gitrub\App\Web\Rest\Response\Response;
use Gitrub\App\Web\Rest\User\Controller\Presenter\UserGithubGatewayErrorPresenter;
use Gitrub\Domain\User\Exception\UserGithubGatewayError;
use Test\Gitrub\GitrubTestCase;

class UserGithubGatewayErrorPresenterTest extends GitrubTestCase {

	public function testAsResponse() : void {
		self::assertEquals(
			new Response(
				httpCode: 500,
				body: '{"error":"Internal github error"}'
			),
			(new UserGithubGatewayErrorPresenter(
				new UserGithubGatewayError('Internal github error')
			))->asResponse()
		);
	}
}
