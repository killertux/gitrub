<?php

namespace Test\Gitrub\App\Web\Rest\User\Controller\Presenter;

use Gitrub\App\Web\Rest\User\Controller\Presenter\UserNotFoundPresenter;
use Gitrub\Domain\User\Exception\UserNotFound;
use Test\Gitrub\GitrubTestCase;

class UserNotFoundPresenterTest extends GitrubTestCase {

	public function testAsResponse(): void {
		$exception = new UserNotFound($message = 'User not found with login test');
		$response = (new UserNotFoundPresenter($exception))->asResponse();
		self::assertEquals(404, $response->httpCode);
		self::assertEquals("{\"error\":\"$message\"}", $response->body);
	}
}
