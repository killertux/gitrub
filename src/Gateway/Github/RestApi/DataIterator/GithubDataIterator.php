<?php

namespace Gitrub\Gateway\Github\RestApi\DataIterator;

use EBANX\Stream\Stream;
use Gitrub\Gateway\Github\RestApi\GithubPersonalAccessToken;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;

class GithubDataIterator implements \IteratorAggregate {

	public function __construct(
		private Client $client,
		private AsRequest $first_request,
		private GithubPersonalAccessToken $personal_access_token,
	) {}

	/**
	 * @return \Iterator|\stdClass[]
	 */
	public function getIterator(): \Iterator {
		return Stream::of($this->internalGenerator())
			->flatten();
	}

	private function internalGenerator(): \Generator {
		$request = $this->first_request->asRequest();
		do {
			$request = self::addAuthenticationIfAvailable($request);
			$response = $this->client->send($request);
			yield json_decode($response->getBody()->getContents());
			$request = (new NextLinkHeaderParser($response->getHeader('Link')[0] ?? null))->toRequest();
		} while ($request !== null);
	}

	private function addAuthenticationIfAvailable(RequestInterface $request): RequestInterface {
		if ($this->personal_access_token->shouldUseToken()) {
			return $request->withAddedHeader('Authorization', $this->personal_access_token->getAuthorizationToken());
		}
		return $request;
	}
}
