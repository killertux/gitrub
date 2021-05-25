<?php

namespace Test\Gitrub\Gateway\Database\Connection;

use Gitrub\Gateway\Database\Connection\DatabaseConnectionData;
use Gitrub\Gateway\Database\Connection\DatabaseConnectionStringParser;
use Test\Gitrub\GitrubTestCase;

class DatabaseConnectionStringParserTest extends GitrubTestCase {

	public function testParse(): void {
		$uri = 'mysql://root:password@db:3306/gitrub_test';
		self::assertEquals(
			new DatabaseConnectionData(
				host: 'db',
				username: 'root',
				password: 'password',
				port: 3306,
				schema: 'gitrub_test'
			),
			(new DatabaseConnectionStringParser($uri))->parse()
		);
	}
}
