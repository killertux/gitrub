<?php

namespace Gitrub\App;

class EnvLoader {

	public function load(): void {
		$env_file = __DIR__ . '/../../config/env.dev.php';
		if (file_exists($env_file)) {
			require_once $env_file;
		}
	}
}
