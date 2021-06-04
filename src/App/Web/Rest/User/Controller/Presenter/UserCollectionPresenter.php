<?php

namespace Gitrub\App\Web\Rest\User\Controller\Presenter;

use Gitrub\App\Web\Rest\Response\AsResponse;
use Gitrub\App\Web\Rest\Response\Response;
use Gitrub\Domain\User\Collection\UserCollection;

class UserCollectionPresenter implements AsResponse {

	public function __construct(
		private UserCollection $user_collection
	) {}

	public function asResponse(): Response {
		return new Response(
			httpCode: 200,
			body: json_encode(
				iterator_to_array($this->user_collection)
			)
		);
	}
}
