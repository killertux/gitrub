<?php


namespace Gitrub\App\Web\GraphQL\Type;


use Gitrub\App\GatewayInstances;
use Gitrub\App\Web\GraphQL\Resolver\Repository\ListRepositoryResolver;
use Gitrub\App\Web\GraphQL\Resolver\Repository\QueryRepositoryResolver;
use Gitrub\App\Web\GraphQL\Resolver\User\ListUserResolver;
use Gitrub\App\Web\GraphQL\Resolver\User\QueryUserResolver;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class QueryType extends ObjectType {

    public function __construct(TypeRegistry $type_registry, GatewayInstances $gateway_instances){
        parent::__construct([
            'name' => 'Query',
            'fields' => [
                'users' => [
                    'type' => Type::listOf($type_registry->userType()),
                    'args' => [
                        'from_limit' => [
                            'type' => $type_registry->fromLimitInputType(),
                            'defaultValue' => ['from' => 0, 'limit' => 50],
                        ]
                    ],
                    'resolve' => fn($_, array $args) => (new ListUserResolver($gateway_instances->user_gateway))->listUsers($args)
                ],
                'admins' => [
                    'type' => Type::listOf($type_registry->userType()),
                    'args' => [
                        'from_limit' => [
                            'type' => $type_registry->fromLimitInputType(),
                            'defaultValue' => ['from' => 0, 'limit' => 50],
                        ]
                    ],
                    'resolve' => fn($_, array $args) => (new ListUserResolver($gateway_instances->user_gateway))->listAdminUsers($args)
                ],
                'user' => [
                    'type' => $type_registry->userType(),
                    'args' => [
                        'id' => [
                            'type' => Type::nonNull(Type::id()),
                        ]
                    ],
                    'resolve' => fn($_, array $args) => (new QueryUserResolver($gateway_instances->user_gateway))->getUserById($args)
                ],
                'userByLogin' => [
                    'type' => $type_registry->userType(),
                    'args' => [
                        'login' => [
                            'type' => Type::nonNull(Type::string()),
                        ]
                    ],
                    'resolve' => fn($_, array $args) => (new QueryUserResolver($gateway_instances->user_gateway))->getUserByLogin($args)
                ],
                'repositories' => [
                    'type' => Type::listOf($type_registry->repositoryType()),
                    'args' => [
                        'from_limit' => [
                            'type' => $type_registry->fromLimitInputType(),
                            'defaultValue' => ['from' => 0, 'limit' => 50],
                        ]
                    ],
                    'resolve' => fn($_, array $args) => (new ListRepositoryResolver($gateway_instances->repository_gateway))->listRepositories($args)
                ],
                'repositoriesWithName' => [
                    'type' => Type::listOf($type_registry->repositoryType()),
                    'args' => [
                        'from_limit' => [
                            'type' => $type_registry->fromLimitInputType(),
                            'defaultValue' => ['from' => 0, 'limit' => 50],
                        ],
                        'name' => [
                            'type' => Type::nonNull(Type::string())
                        ]
                    ],
                    'resolve' => fn($_, array $args) => (new ListRepositoryResolver($gateway_instances->repository_gateway))->listRepositoriesWithName($args)
                ],
                'forks' => [
                    'type' => Type::listOf($type_registry->repositoryType()),
                    'args' => [
                        'from_limit' => [
                            'type' => $type_registry->fromLimitInputType(),
                            'defaultValue' => ['from' => 0, 'limit' => 50],
                        ],
                    ],
                    'resolve' => fn($_, array $args) => (new ListRepositoryResolver($gateway_instances->repository_gateway))->listForkRepositories($args),
                ],
                'repository' => [
                    'type' => $type_registry->repositoryType(),
                    'args' => [
                        'id' => [
                            'type' => Type::nonNull(Type::id()),
                        ],
                    ],
                    'resolve' => fn($_, array $args) => (new QueryRepositoryResolver($gateway_instances->repository_gateway))->getRepositoryById($args),
                ],
                'repositoryByFullName' => [
                    'type' => $type_registry->repositoryType(),
                    'args' => [
                        'full_name' => [
                            'type' => Type::nonNull(Type::string()),
                        ],
                    ],
                    'resolve' => fn($_, array $args) => (new QueryRepositoryResolver($gateway_instances->repository_gateway))->getRepositoryByFullName($args),
                ],
            ],
        ]);
    }
}
