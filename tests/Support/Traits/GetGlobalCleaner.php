<?php

namespace Test\Gitrub\Support\Traits;

trait GetGlobalCleaner {
	/**
	 * @before
	 * @after
	 */
	public function cleanGetGlobals(): void {
		$_GET = [];
	}
}
