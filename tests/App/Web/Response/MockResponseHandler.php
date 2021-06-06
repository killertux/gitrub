<?php

namespace Test\Gitrub\App\Web\Response;

use Gitrub\App\Web\Response\AsResponse;
use Gitrub\App\Web\Response\Response;
use Gitrub\App\Web\Response\ResponseHandler;

class MockResponseHandler implements ResponseHandler {

	public ?Response $last_response = null;

	public function handle(AsResponse $as_response): void {
		$this->last_response = $as_response->asResponse();
	}
}
