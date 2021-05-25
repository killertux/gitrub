<?php

namespace Gitrub\App\Cli;

use ByJG\DbMigration\Database\MySqlDatabase;
use ByJG\DbMigration\Migration;
use ByJG\Util\Uri;

class MigrationHandler {

	public function migrate(): string {
		$migration = $this->internalPreparMigrationHandler(getenv('DATABASE_URI'));
		$migration->update();
		return 'Database is now updated';
	}

	public function reset(?string $option): string {
		$uri = getenv('DATABASE_URI');
		if ($option === 'test') {
			$uri = getenv('DATABASE_TEST_URI');
		}
		$migration = $this->internalPreparMigrationHandler($uri);
		$migration->reset();
		return 'Database was reset';
	}

	private function internalPreparMigrationHandler(string $env): Migration {
	$uri = new Uri($env);

	$migration = new Migration($uri, __DIR__ . '/../../../migrations');
	$migration->registerDatabase('mysql', MySqlDatabase::class);
	$migration->addCallbackProgress(function ($action, $currentVersion, $fileInfo) {
		echo "$action, $currentVersion, ${fileInfo['description']}\n";
	});
	$migration->prepareEnvironment();
	$migration->createVersion();
	return $migration;
}
}
