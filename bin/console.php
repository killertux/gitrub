<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new \Gitrub\App\EnvLoader())->load();

echo (new \Gitrub\App\Cli\CliApp(
	null, //The reason for this, is that we need to first reset the datbases before being able to connect to them
))->run(array_slice($argv, 1)) . PHP_EOL;
