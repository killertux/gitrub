<?php

namespace Gitrub\App\Web\Rest\Response;

interface ResponseHandler {
	public function handle(AsResponse $as_response): void;
}
