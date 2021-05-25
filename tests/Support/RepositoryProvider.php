<?php

namespace Test\Gitrub\Support;

use EBANX\Stream\Stream;
use Faker\Provider\Base;
use Gitrub\Domain\Repository\Entity\Repository;
use Gitrub\Domain\User\Entity\User;

class RepositoryProvider extends Base {

	private $id_sequence = 1;

	public function repository(bool $is_fork = false, User $owner = null, string $name = null): Repository {
		return new Repository(
			id: $this->id_sequence++,
			node_id: $this->generator->unique()->uuid,
			name: $name ?? $this->generator->userName,
			full_name: $this->generator->name,
			owner: $owner ?? $this->generator->user(),
			private: false,
			html_url: $this->generator->url,
			description: $this->generator->text,
			fork: $is_fork,
			url: $this->generator->url,
			archive_url: $this->generator->url,
			assignees_url: $this->generator->url,
			blobs_url: $this->generator->url,
			branches_url: $this->generator->url,
			collaborators_url: $this->generator->url,
			comments_url: $this->generator->url,
			commits_url: $this->generator->url,
			compare_url: $this->generator->url,
			contents_url: $this->generator->url,
			contributors_url: $this->generator->url,
			deployments_url: $this->generator->url,
			downloads_url: $this->generator->url,
			events_url: $this->generator->url,
			forks_url: $this->generator->url,
			git_commits_url: $this->generator->url,
			git_refs_url: $this->generator->url,
			git_tags_url: $this->generator->url,
			git_url: $this->generator->url,
			issue_comment_url: $this->generator->url,
			issues_url: $this->generator->url,
			keys_url: $this->generator->url,
			labels_url: $this->generator->url,
			languages_url: $this->generator->url,
			merges_url: $this->generator->url,
			milestones_url: $this->generator->url,
			notifications_url: $this->generator->url,
			pulls_url: $this->generator->url,
			releases_url: $this->generator->url,
			ssh_url: $this->generator->url,
			stargazers_url: $this->generator->url,
			statuses_url: $this->generator->url,
			subscribers_url: $this->generator->url,
			subscription_url: $this->generator->url,
			tags_url: $this->generator->url,
			teams_url: $this->generator->url,
			trees_url: $this->generator->url,
			hooks_url: $this->generator->url,
		);
	}

	public function createsABunchOfRepositories(
		int $n_repositories,
		bool $fork = false,
		?User $owner = null,
		?string $name = null,
	): array {
		return Stream::rangeInt(1, $n_repositories)
			->map(fn($_) => $this->repository(is_fork: $fork, owner: $owner, name: $name))
			->collect();
	}
}
