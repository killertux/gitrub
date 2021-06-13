<?php

namespace Test\Gitrub\App\Web\Rest;

use Gitrub\App\Web\Request\Request;
use Gitrub\App\Web\Rest\FromLimitFromRequest;
use Gitrub\Domain\General\FromLimit;
use Test\Gitrub\GitrubTestCase;

class FromLimitFromRequestTest extends GitrubTestCase {

	public static function dataProviderForTestFromLimit(): \Generator {
		yield 'without from and limit - should get defaults' => [
			'from' => null,
			'limit' => null,
			'expected_from_limit' => new FromLimit(0, 50)
		];
		yield 'without limit - should get default from' => [
			'from' => 35,
			'limit' => null,
			'expected_from_limit' => new FromLimit(35, 50)
		];
		yield 'without from - should get default limit' => [
			'from' => null,
			'limit' => 30,
			'expected_from_limit' => new FromLimit(0, 30)
		];
		yield 'passing both from and limit' => [
			'from' => 56,
			'limit' => 332,
			'expected_from_limit' => new FromLimit(56, 332)
		];
	}

	/** @dataProvider dataProviderForTestFromLimit */
	public function testFromLimit(?int $from, ?int $limit, FromLimit $expected_from_limit) {
	    $request = new Request(
	        query: ['from' => $from, 'limit' => $limit],
            body: null,
        );
		self::assertEquals($expected_from_limit, (new FromLimitFromRequest(0, 50))->fromLimit($request));
	}
}
