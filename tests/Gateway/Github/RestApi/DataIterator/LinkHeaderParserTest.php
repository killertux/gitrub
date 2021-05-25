<?php

namespace Test\Gitrub\Gateway\Github\RestApi\DataIterator;

use Gitrub\Gateway\Github\RestApi\DataIterator\NextLinkHeaderParser;
use GuzzleHttp\Psr7\Request;
use Test\Gitrub\GitrubTestCase;

class LinkHeaderParserTest extends GitrubTestCase {

	public static function dataProviderForTestToRequest(): \Generator {
		yield [
			'link' => '<https://api.github.com/users?since=46>; rel="next", <https://api.github.com/users{?since}>; rel="first"',
			'expected_request' => new Request('GET', 'https://api.github.com/users?since=46', ['headers' => ['Accept' => 'application/vnd.github.v3+json',]])
		];
		yield [
			'link' => '<https://api.github.com/users?since=557&limit=100>; rel="next", <https://api.github.com/users{?since}>; rel="first"',
			'expected_request' => new Request('GET', 'https://api.github.com/users?since=557&limit=100', ['headers' => ['Accept' => 'application/vnd.github.v3+json',]])
		];
		yield [
			'link' => '<https://api.github.com/users{?since}>; rel="first"',
			'expected_request' => null
		];
		yield [
			'link' => null,
			'expected_request' => null
		];
	}

	/** @dataProvider dataProviderForTestToRequest */
	public function testToRequest(?string $link, ?Request $expected_result): void {
		self::assertEquals(
			$expected_result,
			(new NextLinkHeaderParser($link))
				->toRequest()
		);
	}
}
