<?php

namespace Test\Gitrub\Gateway\Github\RestApi\Repository;

use Gitrub\Gateway\Github\RestApi\Repository\RepositoryFromDecodedObject;
use Test\Gitrub\GitrubTestCase;

class RepositoryFromDecodedObjectTest extends GitrubTestCase {

	public function testRepository() {
		$repository = $this->faker->repository();
		$encoded_repository = json_encode($repository);
		self::assertEquals(
			$repository,
			(new RepositoryFromDecodedObject(json_decode($encoded_repository)))->repository()
		);
	}
}
