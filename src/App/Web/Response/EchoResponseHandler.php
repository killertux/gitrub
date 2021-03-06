<?php

namespace Gitrub\App\Web\Response;

class EchoResponseHandler implements ResponseHandler {

	public function handle(AsResponse $as_response): void {
		$response = $as_response->asResponse();
		http_response_code($response->http_code);
		header('Content-Type: application/json');
		echo $response->body;
	}
}
