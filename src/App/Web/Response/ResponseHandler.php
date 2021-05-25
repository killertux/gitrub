<?php

namespace Gitrub\App\Web\Response;

interface ResponseHandler {
	public function handle(AsResponse $as_response): void;
}
