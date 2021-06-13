<?php


namespace Gitrub\App\Web\GraphQL\Resolver\Repository;


use Gitrub\Domain\Repository\Entity\Repository;
use Gitrub\Domain\Repository\Gateway\RepositoryGateway;
use Gitrub\Domain\Repository\UseCase\QueryRepositoryUseCase;

class QueryRepositoryResolver {

    public function __construct(private RepositoryGateway $repository_gateway) {}

    public function getRepositoryById(array $args): Repository {
        return (new QueryRepositoryUseCase($this->repository_gateway))
            ->getRepositoryById($args['id']);
    }

    public function getRepositoryByFullName(array $args): Repository {
        return (new QueryRepositoryUseCase($this->repository_gateway))
            ->getRepositoryByFullName($args['full_name']);
    }


}
