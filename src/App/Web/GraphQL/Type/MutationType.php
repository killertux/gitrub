<?php


namespace Gitrub\App\Web\GraphQL\Type;


use Gitrub\App\GatewayInstances;
use Gitrub\App\Web\GraphQL\Resolver\Repository\ScrapeRepositoryResolver;
use Gitrub\App\Web\GraphQL\Resolver\User\ScrapeUserResolver;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class MutationType extends ObjectType {

    public function __construct(TypeRegistry $type_registry, GatewayInstances $gateway_instances) {
        parent::__construct([
            'name' => 'Mutation',
            'fields' => [
                'scrapeUsers' => [
                    'type' => Type::string(),
                    'args' => [
                        'from_limit' => Type::nonNull($type_registry->fromLimitInputType()),
                    ],
                    'resolve' => fn($_, array $args) =>
                        (new ScrapeUserResolver(
                            $gateway_instances->user_gateway,
                            $gateway_instances->user_github_gateway,
                            $gateway_instances->user_scrape_state_gateway
                        ))->scrapeUsers($args),
                ],
                'continueToScrapeUsers' => [
                    'type' => Type::string(),
                    'args' => [
                        'limit' => Type::nonNull(Type::int()),
                    ],
                    'resolve' => fn($_, array $args) =>
                        (new ScrapeUserResolver(
                            $gateway_instances->user_gateway,
                            $gateway_instances->user_github_gateway,
                            $gateway_instances->user_scrape_state_gateway
                        ))->continueToScrapeUsers($args),
                ],
                'scrapeRepositories' => [
                    'type' => Type::string(),
                    'args' => [
                        'from_limit' => Type::nonNull($type_registry->fromLimitInputType()),
                    ],
                    'resolve' => fn($_, array $args) =>
                        (new ScrapeRepositoryResolver(
                            $gateway_instances->repository_gateway,
                            $gateway_instances->repository_github_gateway,
                            $gateway_instances->repository_scrape_state_gateway
                        ))->scrapeRepositories($args),
                ],
                'continueToScrapeRepositories' => [
                    'type' => Type::string(),
                    'args' => [
                        'limit' => Type::nonNull(Type::int()),
                    ],
                    'resolve' => fn($_, array $args) =>
                        (new ScrapeRepositoryResolver(
                            $gateway_instances->repository_gateway,
                            $gateway_instances->repository_github_gateway,
                            $gateway_instances->repository_scrape_state_gateway
                        ))->continueToScrapeRepositories($args),
                ],
            ],
        ]);
    }
}
