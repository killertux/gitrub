<?php


namespace Gitrub\App\Web\GraphQL\Type\User;


use Gitrub\App\Web\GraphQL\Resolver\Repository\ListRepositoryResolver;
use Gitrub\App\Web\GraphQL\Type\TypeRegistry;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\User\Entity\User;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class UserType extends ObjectType {

    public function __construct(TypeRegistry $type_registry, RepositoryGateway $repository_gateway){
        parent::__construct([
            'name' => 'User',
            'description' => 'User type from GitHub',
            'fields' => function() use ($type_registry, $repository_gateway): array{
                return [
                    'id' => Type::nonNull(Type::id()),
                    'login' => Type::nonNull(Type::string()),
                    'node_id' => Type::string(),
                    'avatar_url' => Type::string(),
                    'gravatar_id' => Type::string(),
                    'url' => Type::string(),
                    'html_url' => Type::string(),
                    'followers_url' => Type::string(),
                    'following_url' => Type::string(),
                    'gists_url' => Type::string(),
                    'starred_url' => Type::string(),
                    'subscriptions_url' => Type::string(),
                    'organizations_url' => Type::string(),
                    'repos_url' => Type::string(),
                    'events_url' => Type::string(),
                    'received_events_url' => Type::string(),
                    'type' => Type::string(),
                    'site_admin' => Type::boolean(),
                    'repositories' => [
                        'type' => Type::listOf($type_registry->repositoryType()),
                        'args' => [
                            'from_limit' => [
                                'type' => $type_registry->fromLimitInputType(),
                                'defaultValue' => ['from' => 0, 'limit' => 50],
                            ]
                        ],
                        'resolve' => fn(User $user, array $args) => (new ListRepositoryResolver($repository_gateway))->listRepositoriesWithOwner($user->id, $args)
                    ]
                ];
            }
        ]);
    }
}
