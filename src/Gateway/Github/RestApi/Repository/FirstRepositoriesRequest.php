<?php

namespace Gitrub\Gateway\Github\RestApi\Repository;

use Gitrub\Gateway\Github\RestApi\DataIterator\AsRequest;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

class FirstRepositoriesRequest implements AsRequest {

	public function __construct(
		private int $from_id
	) {}

	public function asRequest(): RequestInterface {
		return new Request(
			'GET',
			Uri::withQueryValues(new Uri('https://api.github.com/repositories'), [
				'since' => $this->from_id,
			]),
			['Accept' => 'application/vnd.github.v3+json']
		);
	}
}
