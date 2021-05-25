<?php

namespace Test\Gitrub\App\Web\User\Controller\Presenter;

use Gitrub\App\Web\User\Controller\Presenter\UserPresenter;
use Test\Gitrub\GitrubTestCase;

class UserPresenterTest extends GitrubTestCase {

	public function testAsResponse(): void {
		$user = $this->faker->user();
		$response = (new UserPresenter($user))->asResponse();
		self::assertEquals(200, $response->httpCode);
		self::assertEquals(json_encode($user), $response->body);
	}
}
