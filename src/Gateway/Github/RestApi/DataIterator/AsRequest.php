<?php

namespace Gitrub\Gateway\Github\RestApi\DataIterator;

use Psr\Http\Message\RequestInterface;

interface AsRequest {

	public function asRequest(): RequestInterface;
}
