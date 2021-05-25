<?php

namespace Gitrub\Gateway\Github\RestApi;

use EBANX\Stream\Stream;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Gitrub\Domain\Repository\Exception\RepositoryGithubGatewayError;
use Gitrub\Domain\Repository\Gateway\RepositoryGithubGateway;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Exception\UserGithubGatewayError;
use Gitrub\Domain\User\Gateway\UserGithubGateway;
use Gitrub\Gateway\Github\RestApi\Repository\FirstRepositoriesRequest;
use Gitrub\Gateway\Github\RestApi\Repository\RepositoryFromDecodedObject;
use Gitrub\Gateway\Github\RestApi\User\FirstUsersRequest;
use Gitrub\Gateway\Github\RestApi\DataIterator\GithubDataIterator;
use Gitrub\Gateway\Github\RestApi\User\UserFromDecodedObject;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class GithubRestAPIGateway implements UserGithubGateway, RepositoryGithubGateway {


	public function __construct (
		private Client $client,
		private GithubPersonalAccessToken $personal_access_token,
	) {}

	public function listUsers(FromLimit $from_limit): UserCollection {
		try {
			return new UserCollection(
				Stream::of(
					self::getDataIteratorAndWrapUserException(
						new GithubDataIterator($this->client, new FirstUsersRequest($from_limit->from), $this->personal_access_token)
					)
				)
				->map(fn(\stdClass $user_data) => (new UserFromDecodedObject($user_data))->user())
				->take($from_limit->limit)
			);
		} catch (RequestException $request_exception) {
			throw new UserGithubGatewayError($request_exception->getMessage());
		}
	}

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

	private static function getDataIteratorAndWrapUserException(GithubDataIterator $iterator): \Generator {
		try {
			foreach ($iterator as $item) {
				yield $item;
			}
		} catch (RequestException $request_exception) {
			throw new UserGithubGatewayError($request_exception->getMessage());
		}
	}
}
