<?php

namespace Test\Gitrub\Support;

use EBANX\Stream\Stream;
use Faker\Provider\Base;
use Gitrub\Domain\User\Entity\User;

class UserProvider extends Base {

	private $id_sequence = 1;

	public function user(): User {
		return new User(
			login: $this->generator->unique()->userName,
			id: $this->id_sequence++,
			node_id: $this->generator->uuid,
			avatar_url: $this->generator->url,
			gravatar_id: $this->generator->uuid,
			url: $this->generator->url,
			html_url: $this->generator->url,
			followers_url: $this->generator->url,
			following_url: $this->generator->url,
			gists_url: $this->generator->url,
			starred_url: $this->generator->url,
			subscriptions_url: $this->generator->url,
			organizations_url: $this->generator->url,
			repos_url: $this->generator->url,
			events_url: $this->generator->url,
			received_events_url: $this->generator->url,
			type: 'User',
			site_admin: false,
		);
	}

	public function adminUser(): User {
		$user = $this->user();
		$user->site_admin = true;
		return $user;
	}

	public function createsABunchOfUsers(int $n_users): array {
		return Stream::rangeInt(1, $n_users)
			->map(fn($_) => $this->user())
			->collect();
	}

	public function createsABunchOfAdminUsers(int $n_users): array {
		return Stream::rangeInt(1, $n_users)
			->map(fn($_) => $this->adminUser())
			->collect();
	}
}
