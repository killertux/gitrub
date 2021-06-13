<?php


namespace Gitrub\App\Web\GraphQL\Resolver\User;


use Gitrub\Domain\General\FromLimit;
use Gitrub\Domain\User\Collection\UserCollection;
use Gitrub\Domain\User\Gateway\UserGateway;
use Gitrub\Domain\User\UseCase\ListUserUseCase;

class ListUserResolver {

    public function __construct(
        private UserGateway $user_gateway,
    ) {}

    public function listUsers(array $args): UserCollection {
        return (new ListUserUseCase($this->user_gateway))
            ->listUsers(new FromLimit($args['from_limit']['from'], $args['from_limit']['limit']));
    }

    public function listAdminUsers(array $args): UserCollection {
        return (new ListUserUseCase($this->user_gateway))
            ->listAdminUsers(new FromLimit($args['from_limit']['from'], $args['from_limit']['limit']));
    }
}
