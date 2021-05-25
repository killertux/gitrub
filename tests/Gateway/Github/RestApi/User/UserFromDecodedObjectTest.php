<?php

namespace Test\Gitrub\Gateway\Github\RestApi\User;

use Gitrub\Gateway\Github\RestApi\User\UserFromDecodedObject;
use Test\Gitrub\GitrubTestCase;

class UserFromDecodedObjectTest extends GitrubTestCase {

	public function testUser() {
		$user = $this->faker->user();
		$encoded_user = json_encode($user);
		self::assertEquals(
			$user,
			(new UserFromDecodedObject(json_decode($encoded_user)))->user()
		);
	}
}
