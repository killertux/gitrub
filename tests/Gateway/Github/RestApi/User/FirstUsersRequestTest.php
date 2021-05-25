<?php

namespace Test\Gitrub\Gateway\Github\RestApi\User;

use Gitrub\Gateway\Github\RestApi\User\FirstUsersRequest;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Test\Gitrub\GitrubTestCase;

class FirstUsersRequestTest extends GitrubTestCase {

	public function testAsRequest() {
		self::assertEquals(
			new Request('GET', new Uri('https://api.github.com/users?since=0&limit=100'), ['Accept' => 'application/vnd.github.v3+json']),
			(new FirstUsersRequest(0))->asRequest()
		);
		self::assertEquals(
			new Request('GET', new Uri('https://api.github.com/users?since=100&limit=100'), ['Accept' => 'application/vnd.github.v3+json']),
			(new FirstUsersRequest(100))->asRequest()
		);
	}
}
