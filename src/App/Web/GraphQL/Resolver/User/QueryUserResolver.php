<?php


namespace Gitrub\App\Web\GraphQL\Resolver\User;


use Gitrub\Domain\User\Entity\User;
use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Domain\User\UseCase\QueryUserUseCase;

class QueryUserResolver {

    public function __construct(private UserGateway $user_gateway) {}

    public function getUserById(array $args): User {
        return (new QueryUserUseCase($this->user_gateway))->getUserById($args['id']);
    }

    public function getUserByLogin(array $args): User {
        return (new QueryUserUseCase($this->user_gateway))->getUserByLogin($args['login']);
    }
}
