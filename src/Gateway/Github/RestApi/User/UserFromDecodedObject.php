<?php

namespace Gitrub\Gateway\Github\RestApi\User;

use Gitrub\Domain\User\Entity\User;

class UserFromDecodedObject {

	public function __construct(
		private \stdClass $decoded_object
	) {}

	public function user(): User {
		return new User(
			login: $this->decoded_object->login,
			id: $this->decoded_object->id,
			node_id: $this->decoded_object->node_id,
			avatar_url: $this->decoded_object->avatar_url,
			gravatar_id: $this->decoded_object->gravatar_id,
			url: $this->decoded_object->url,
			html_url: $this->decoded_object->html_url,
			followers_url: $this->decoded_object->followers_url,
			following_url: $this->decoded_object->following_url,
			gists_url: $this->decoded_object->gists_url,
			starred_url: $this->decoded_object->starred_url,
			subscriptions_url: $this->decoded_object->subscriptions_url,
			organizations_url: $this->decoded_object->organizations_url,
			repos_url: $this->decoded_object->repos_url,
			events_url: $this->decoded_object->events_url,
			received_events_url: $this->decoded_object->received_events_url,
			type: $this->decoded_object->type,
			site_admin: $this->decoded_object->site_admin,
		);
	}
}
