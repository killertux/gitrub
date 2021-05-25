<?php

namespace Gitrub\Gateway\Database\Connection;

class DatabaseConnectionData {

	public function __construct(
		public string $host,
		public string $username,
		public string $password,
		public int $port,
		public string $schema
	) {}
}
