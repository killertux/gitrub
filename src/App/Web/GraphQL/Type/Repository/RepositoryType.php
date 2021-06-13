<?php


namespace Gitrub\App\Web\GraphQL\Type\Repository;


use Gitrub\App\Web\GraphQL\Type\TypeRegistry;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class RepositoryType extends ObjectType {

    public function __construct(TypeRegistry $type_registry){
        parent::__construct([
            'name' => 'Repository',
            'description' => 'Repository type from GitHub',
            'fields' => [
                'id' => Type::nonNull(Type::id()),
                'name' => Type::nonNull(Type::string()),
                'full_name' => Type::nonNull(Type::string()),
                'owner' => Type::nonNull($type_registry->userType()),
                'private' => Type::boolean(),
                'html_url' => Type::string(),
                'description' => Type::string(),
                'fork' => Type::boolean(),
                'url' => Type::string(),
                'archive_url' => Type::string(),
                'assignees_url' => Type::string(),
                'blobs_url' => Type::string(),
                'branches_url' => Type::string(),
                'collaborators_url' => Type::string(),
                'comments_url' => Type::string(),
                'commits_url' => Type::string(),
                'compare_url' => Type::string(),
                'contents_url' => Type::string(),
                'contributors_url' => Type::string(),
                'deployments_url' => Type::string(),
                'downloads_url' => Type::string(),
                'events_url' => Type::string(),
                'forks_url' => Type::string(),
                'git_commits_url' => Type::string(),
                'git_refs_url' => Type::string(),
                'git_tags_url' => Type::string(),
                'git_url' => Type::string(),
                'issue_comment_url' => Type::string(),
                'issues_url' => Type::string(),
                'keys_url' => Type::string(),
                'labels_url' => Type::string(),
                'languages_url' => Type::string(),
                'merges_url' => Type::string(),
                'milestones_url' => Type::string(),
                'notifications_url' => Type::string(),
                'pulls_url' => Type::string(),
                'releases_url' => Type::string(),
                'ssh_url' => Type::string(),
                'stargazers_url' => Type::string(),
                'statuses_url' => Type::string(),
                'subscribers_url' => Type::string(),
                'subscription_url' => Type::string(),
                'tags_url' => Type::string(),
                'teams_url' => Type::string(),
                'trees_url' => Type::string(),
                'hooks_url' => Type::string(),
            ]
        ]);
    }
}
