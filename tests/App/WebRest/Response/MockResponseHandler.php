<?php

namespace Test\Gitrub\App\WebRest\Response;

use Gitrub\App\Web\Rest\Response\AsResponse;
use Gitrub\App\Web\Rest\Response\Response;
use Gitrub\App\Web\Rest\Response\ResponseHandler;

class MockResponseHandler implements ResponseHandler {

	public ?Response $last_response = null;

	public function handle(AsResponse $as_response): void {
		$this->last_response = $as_response->asResponse();
	}
}
