<?php

namespace Test\Gitrub\Support\Traits;

trait ServerGlobalCleaner {

	/**
	 * @before
	 * @after
	 */
	public function cleanServerGlobals(): void {
		$_SERVER['REQUEST_URI'] = null;
		$_SERVER['REQUEST_METHOD'] = null;
	}
}
