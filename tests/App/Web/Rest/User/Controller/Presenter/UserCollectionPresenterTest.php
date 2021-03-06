<?php

namespace Test\Gitrub\App\Web\Rest\User\Controller\Presenter;

use Gitrub\App\Web\Rest\User\Controller\Presenter\UserCollectionPresenter;
use Gitrub\Domain\User\Collection\UserCollection;
use Test\Gitrub\GitrubTestCase;

class UserCollectionPresenterTest extends GitrubTestCase {

	public function testAsResponseEmptyCollection(): void {
		$collection = new UserCollection(new \ArrayIterator([]));
		$response = (new UserCollectionPresenter($collection))->asResponse();
		self::assertEquals(200, $response->http_code);
		self::assertEquals('[]', $response->body);
	}

	public function testAsResponse(): void {
		$users = [$this->faker->user(), $this->faker->user()];
		$response = (new UserCollectionPresenter(new UserCollection(new \ArrayIterator($users))))
			->asResponse();
		self::assertEquals(200, $response->http_code);
		self::assertEquals(json_encode($users), $response->body);
	}
}
