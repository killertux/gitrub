<?php

namespace Gitrub\Gateway\Github\RestApi\Repository;

use EBANX\Stream\Stream;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Gitrub\Domain\Repository\Exception\RepositoryGithubGatewayError;
use Gitrub\Domain\Repository\Gateway\RepositoryGithubGateway;
use Gitrub\Gateway\Github\RestApi\DataIterator\GithubDataIterator;
use Gitrub\Gateway\Github\RestApi\GithubPersonalAccessToken;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class RestAPIRepositoryGithubGateway implements RepositoryGithubGateway {
	public function __construct (
		private Client $client,
		private GithubPersonalAccessToken $personal_access_token,
	) {}

	/** @throws RepositoryGithubGatewayError */
	public function listRepositories(FromLimit $from_limit): RepositoryCollection {
		return new RepositoryCollection(
			Stream::of(
				self::getDataIteratorAndWrapRepositoryException(
					new GithubDataIterator($this->client, new FirstRepositoriesRequest($from_limit->from), $this->personal_access_token)
				)
			)
				->map(fn(\stdClass $repository_data) => (new RepositoryFromDecodedObject($repository_data))->repository())
				->take($from_limit->limit)
		);
	}

	private static function getDataIteratorAndWrapRepositoryException(GithubDataIterator $iterator): \Generator {
		try {
			foreach ($iterator as $item) {
				yield $item;
			}
		} catch (RequestException $request_exception) {
			throw new RepositoryGithubGatewayError($request_exception->getMessage());
		}
	}
}
