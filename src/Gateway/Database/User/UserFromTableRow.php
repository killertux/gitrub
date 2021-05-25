<?php

namespace Gitrub\Gateway\Database\User;

use Gitrub\Domain\User\Entity\User;

class UserFromTableRow {

	private \stdClass $data_object;

	public function __construct(array $data_object) {
		$this->data_object = (object)$data_object;
	}

	public function user(): User {
		return new User(
			login: $this->data_object->login,
			id: $this->data_object->id,
			node_id: $this->data_object->node_id,
			avatar_url: $this->data_object->avatar_url,
			gravatar_id: $this->data_object->gravatar_id,
			url: $this->data_object->url,
			html_url: $this->data_object->html_url,
			followers_url: $this->data_object->followers_url,
			following_url: $this->data_object->following_url,
			gists_url: $this->data_object->gists_url,
			starred_url: $this->data_object->starred_url,
			subscriptions_url: $this->data_object->subscriptions_url,
			organizations_url: $this->data_object->organizations_url,
			repos_url: $this->data_object->repos_url,
			events_url: $this->data_object->events_url,
			received_events_url: $this->data_object->received_events_url,
			type: $this->data_object->type,
			site_admin: $this->data_object->site_admin,
		);
	}
}
