<?php

namespace Gitrub\Gateway\Database\Connection;

class DatabaseConnectionStringParser {

	public function __construct(
		private string $connection_string,
	) {}

	public function parse(): DatabaseConnectionData {
		[$_uri, $rest]= explode('://', $this->connection_string, 2);
		[$user_data, $host_data] = explode('@', $rest, 2);
		[$username, $password] = explode(':', $user_data, 2);
		[$host, $port_and_schema] = explode(':', $host_data);
		[$port, $schema] = explode('/', $port_and_schema);
		return new DatabaseConnectionData(
			host: $host,
			username: $username,
			password: $password,
			port: (int)$port,
			schema: $schema
		);
	}
}
