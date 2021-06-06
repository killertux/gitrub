<?php

namespace Test\Gitrub\App\Web\Rest\Presenter;

use Gitrub\App\Web\Rest\Presenter\InvalidArgumentExceptionPresenter;
use Test\Gitrub\GitrubTestCase;

class InvalidArgumentExceptionPresenterTest extends GitrubTestCase {

	public function testAsResponse(): void {
		$response = (new InvalidArgumentExceptionPresenter(
			new \InvalidArgumentException("Limit must be a positive number"),
		))->asResponse();
		self::assertEquals(400, $response->httpCode);
		self::assertEquals(
			'{"error":"Limit must be a positive number"}',
			$response->body
		);
	}
}
