<?php

namespace Gitrub\Gateway\Github\RestApi\User;

use Gitrub\Gateway\Github\RestApi\DataIterator\AsRequest;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

class FirstUsersRequest implements AsRequest {

	public function __construct(
		private int $from_id,
	) {}

	public function asRequest(): RequestInterface {
		return new Request(
			'GET',
			Uri::withQueryValues(new Uri('https://api.github.com/users'), [
				'since' => $this->from_id,
				'limit' => 100
			]),
			['Accept' => 'application/vnd.github.v3+json']
		);
	}
}
