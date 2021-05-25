<?php

namespace Gitrub\Domain\Repository\Entity;

use Gitrub\Domain\User\Entity\User;

class Repository {

	public function __construct(
		public int $id,
		public string $node_id,
		public string $name,
		public string $full_name,
		public User $owner,
		public bool $private,
		public string $html_url,
		public ?string $description,
		public bool $fork,
		public string $url,
		public string $archive_url,
		public string $assignees_url,
		public string $blobs_url,
		public string $branches_url,
		public string $collaborators_url,
		public string $comments_url,
		public string $commits_url,
		public string $compare_url,
		public string $contents_url,
		public string $contributors_url,
		public string $deployments_url,
		public string $downloads_url,
		public string $events_url,
		public string $forks_url,
		public string $git_commits_url,
		public string $git_refs_url,
		public string $git_tags_url,
		public ?string $git_url,
		public string $issue_comment_url,
		public string $issues_url,
		public string $keys_url,
		public string $labels_url,
		public string $languages_url,
		public string $merges_url,
		public string $milestones_url,
		public string $notifications_url,
		public string $pulls_url,
		public string $releases_url,
		public ?string $ssh_url,
		public string $stargazers_url,
		public string $statuses_url,
		public string $subscribers_url,
		public string $subscription_url,
		public string $tags_url,
		public string $teams_url,
		public string $trees_url,
		public string $hooks_url,
	) {}
}
