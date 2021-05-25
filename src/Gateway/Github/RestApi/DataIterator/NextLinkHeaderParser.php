<?php

namespace Gitrub\Gateway\Github\RestApi\DataIterator;

use GuzzleHttp\Psr7\Request;
use TiagoHillebrandt\ParseLinkHeader;

class NextLinkHeaderParser {

	public function __construct(
		private ?string $link_header
	) {}

	public function toRequest(): ?Request {
		if ($this->link_header === null) {
			return null;
		}
		$next_link = (new ParseLinkHeader($this->link_header))
			->toArray()['next']['link'] ?? null;
		return $next_link === null ? $next_link :
			new Request('GET', $next_link, ['headers' => ['Accept' => 'application/vnd.github.v3+json',]]);
	}
}
