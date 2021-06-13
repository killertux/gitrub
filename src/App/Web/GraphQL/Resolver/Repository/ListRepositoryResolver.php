<?php


namespace Gitrub\App\Web\GraphQL\Resolver\Repository;


use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\Repository\Collection\RepositoryCollection;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\Repository\UseCase\ListRepositoryUseCase;

class ListRepositoryResolver {

    public function __construct(private RepositoryGateway $repository_gateway) {}

    public function listRepositories(array $args): RepositoryCollection {
        return (new ListRepositoryUseCase($this->repository_gateway))
            ->listRepositories(new FromLimit($args['from_limit']['from'], $args['from_limit']['limit']));
    }

    public function listRepositoriesWithOwner(string $owner_id, array $args): RepositoryCollection {
        return (new ListRepositoryUseCase($this->repository_gateway))
            ->listRepositoriesFromOwner($owner_id, new FromLimit($args['from_limit']['from'], $args['from_limit']['limit']));
    }

    public function listRepositoriesWithName(array $args): RepositoryCollection {
        return (new ListRepositoryUseCase($this->repository_gateway))
            ->listRepositoriesWithName($args['name'], new FromLimit($args['from_limit']['from'], $args['from_limit']['limit']));
    }

    public function listForkRepositories(array $args): RepositoryCollection {
        return (new ListRepositoryUseCase($this->repository_gateway))
            ->listForkRepositories(new FromLimit($args['from_limit']['from'], $args['from_limit']['limit']));
    }
}
