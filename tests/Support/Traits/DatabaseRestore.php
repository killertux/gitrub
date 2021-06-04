<?php

namespace Test\Gitrub\Support\Traits;

use Gitrub\App\EnvLoader;
use Gitrub\Gateway\Database\Connection\DatabaseConnectionStringParser;
use Gitrub\Gateway\Database\Connection\PdoFromDatabaseConnectionData;

trait DatabaseRestore {

	protected \PDO $pdo;

	/** @before */
	public function setUpDatabase(): void {
		(new EnvLoader())->load();
		$connection_string = getenv('DATABASE_TEST_URI');
		$this->pdo = (new PdoFromDatabaseConnectionData((new DatabaseConnectionStringParser($connection_string))->parse()))
			->connect();
		$this->pdo->exec('START TRANSACTION;');
		$this->pdo->exec('SAVEPOINT test_start;');
	}

	/** @after */
	public function rollback(): void {
		$this->pdo->exec('ROLLBACK TO SAVEPOINT test_start;');
	}
}
