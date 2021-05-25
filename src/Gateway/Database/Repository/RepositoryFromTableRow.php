<?php

namespace Gitrub\Gateway\Database\Repository;

use EBANX\Stream\Stream;
use Gitrub\Domain\Repository\Entity\Repository;
use Gitrub\Domain\User\Entity\User;
use Gitrub\Gateway\Database\User\UserFromTableRow;

class RepositoryFromTableRow {

	private \stdClass $repository_data;

	public function __construct(array $repository_data) {
		$this->repository_data = (object)$repository_data;
	}

	public function repository(): Repository {
		$owner = $this->createOwner();
		return new Repository(
			id: $this->repository_data->id,
			node_id: $this->repository_data->node_id,
			name: $this->repository_data->name,
			full_name: $this->repository_data->full_name,
			owner: $owner,
			private: $this->repository_data->private,
			html_url: $this->repository_data->html_url,
			description: $this->repository_data->description ?? null,
			fork: $this->repository_data->fork,
			url: $this->repository_data->url,
			archive_url: $this->repository_data->archive_url,
			assignees_url: $this->repository_data->assignees_url,
			blobs_url: $this->repository_data->blobs_url,
			branches_url: $this->repository_data->branches_url,
			collaborators_url: $this->repository_data->collaborators_url,
			comments_url: $this->repository_data->comments_url,
			commits_url: $this->repository_data->commits_url,
			compare_url: $this->repository_data->compare_url,
			contents_url: $this->repository_data->contents_url,
			contributors_url: $this->repository_data->contributors_url,
			deployments_url: $this->repository_data->deployments_url,
			downloads_url: $this->repository_data->downloads_url,
			events_url: $this->repository_data->events_url,
			forks_url: $this->repository_data->forks_url,
			git_commits_url: $this->repository_data->git_commits_url,
			git_refs_url: $this->repository_data->git_refs_url,
			git_tags_url: $this->repository_data->git_tags_url,
			git_url: $this->repository_data->git_url ?? null,
			issue_comment_url: $this->repository_data->issue_comment_url,
			issues_url: $this->repository_data->issues_url,
			keys_url: $this->repository_data->keys_url,
			labels_url: $this->repository_data->labels_url,
			languages_url: $this->repository_data->languages_url,
			merges_url: $this->repository_data->merges_url,
			milestones_url: $this->repository_data->milestones_url,
			notifications_url: $this->repository_data->notifications_url,
			pulls_url: $this->repository_data->pulls_url,
			releases_url: $this->repository_data->releases_url,
			ssh_url: $this->repository_data->ssh_url ?? null,
			stargazers_url: $this->repository_data->stargazers_url,
			statuses_url: $this->repository_data->statuses_url,
			subscribers_url: $this->repository_data->subscribers_url,
			subscription_url: $this->repository_data->subscription_url,
			tags_url: $this->repository_data->tags_url,
			teams_url: $this->repository_data->teams_url,
			trees_url: $this->repository_data->trees_url,
			hooks_url: $this->repository_data->hooks_url,
		);
	}

	private function createOwner(): User {
		$data = (array)$this->repository_data;
		return (new UserFromTableRow(
			Stream::ofKeyValueMap($data)
				->filter(fn(array $key_value) => str_starts_with($key_value[0], 'owner_'))
				->map(function (array $key_value): array {
					[$key, $value] = $key_value;
					return [str_replace('owner_', '', $key), $value];
				})
				->collectAsKeyValue()
		))->user();
	}
}
