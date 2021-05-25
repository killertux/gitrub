<?php

namespace Gitrub\Gateway\Database\Connection;

class PdoFromDatabaseConnectionData {

	public function __construct(
		private DatabaseConnectionData $database_connection_data
	) {}

	public function connect(): \PDO {
		return new \PDO(
			"mysql:dbname={$this->database_connection_data->schema};host={$this->database_connection_data->host};port={$this->database_connection_data->port}",
			$this->database_connection_data->username,
			$this->database_connection_data->password
		);
	}
}
