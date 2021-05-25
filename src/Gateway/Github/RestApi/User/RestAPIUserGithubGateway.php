<?php

namespace Gitrub\Gateway\Github\RestApi\User;

use EBANX\Stream\Stream;
use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Exception\UserGithubGatewayError;
use Gitrub\Domain\User\Gateway\UserGithubGateway;
use Gitrub\Gateway\Github\RestApi\DataIterator\GithubDataIterator;
use Gitrub\Gateway\Github\RestApi\GithubPersonalAccessToken;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class RestAPIUserGithubGateway implements UserGithubGateway {

	public function __construct (
		private Client $client,
		private GithubPersonalAccessToken $personal_access_token,
	) {}

	/** @throws UserGithubGatewayError */
	public function listUsers(FromLimit $from_limit): UserCollection {
		return new UserCollection(
			Stream::of(
				self::getDataIteratorAndWrapUserException(
					new GithubDataIterator($this->client, new FirstUsersRequest($from_limit->from), $this->personal_access_token)
				)
			)
				->map(fn(\stdClass $user_data) => (new UserFromDecodedObject($user_data))->user())
				->take($from_limit->limit)
		);
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
