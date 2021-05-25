<?php

namespace Gitrub\Gateway\Github\RestApi\Repository;

use Gitrub\Domain\Repository\Entity\Repository;
use Gitrub\Gateway\Github\RestApi\User\UserFromDecodedObject;

class RepositoryFromDecodedObject {

	public function __construct(
		private \stdClass $decoded_object
	) {}

	public function repository(): Repository {
		return new Repository(
			id: $this->decoded_object->id,
			node_id: $this->decoded_object->node_id,
			name: $this->decoded_object->name,
			full_name: $this->decoded_object->full_name,
			owner: (new UserFromDecodedObject($this->decoded_object->owner))->user(),
			private: $this->decoded_object->private,
			html_url: $this->decoded_object->html_url,
			description: $this->decoded_object->description ?? null,
			fork: $this->decoded_object->fork,
			url: $this->decoded_object->url,
			archive_url: $this->decoded_object->archive_url,
			assignees_url: $this->decoded_object->assignees_url,
			blobs_url: $this->decoded_object->blobs_url,
			branches_url: $this->decoded_object->branches_url,
			collaborators_url: $this->decoded_object->collaborators_url,
			comments_url: $this->decoded_object->comments_url,
			commits_url: $this->decoded_object->commits_url,
			compare_url: $this->decoded_object->compare_url,
			contents_url: $this->decoded_object->contents_url,
			contributors_url: $this->decoded_object->contributors_url,
			deployments_url: $this->decoded_object->deployments_url,
			downloads_url: $this->decoded_object->downloads_url,
			events_url: $this->decoded_object->events_url,
			forks_url: $this->decoded_object->forks_url,
			git_commits_url: $this->decoded_object->git_commits_url,
			git_refs_url: $this->decoded_object->git_refs_url,
			git_tags_url: $this->decoded_object->git_tags_url,
			git_url: $this->decoded_object->git_url ?? null,
			issue_comment_url: $this->decoded_object->issue_comment_url,
			issues_url: $this->decoded_object->issues_url,
			keys_url: $this->decoded_object->keys_url,
			labels_url: $this->decoded_object->labels_url,
			languages_url: $this->decoded_object->languages_url,
			merges_url: $this->decoded_object->merges_url,
			milestones_url: $this->decoded_object->milestones_url,
			notifications_url: $this->decoded_object->notifications_url,
			pulls_url: $this->decoded_object->pulls_url,
			releases_url: $this->decoded_object->releases_url,
			ssh_url: $this->decoded_object->ssh_url ?? null,
			stargazers_url: $this->decoded_object->stargazers_url,
			statuses_url: $this->decoded_object->statuses_url,
			subscribers_url: $this->decoded_object->subscribers_url,
			subscription_url: $this->decoded_object->subscription_url,
			tags_url: $this->decoded_object->tags_url,
			teams_url: $this->decoded_object->teams_url,
			trees_url: $this->decoded_object->trees_url,
			hooks_url: $this->decoded_object->hooks_url,
		);
	}
}
