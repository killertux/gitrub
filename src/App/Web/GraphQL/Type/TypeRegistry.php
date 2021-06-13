<?php


namespace Gitrub\App\Web\GraphQL\Type;


use Gitrub\App\GatewayInstances;
use Gitrub\App\Web\GraphQL\Type\Repository\RepositoryType;
use Gitrub\App\Web\GraphQL\Type\User\UserType;

class TypeRegistry {

    private UserType $user_type;
    private RepositoryType $repository_type;
    private FromLimitInputType $from_limit_type;

    public function __construct(
        private GatewayInstances $gateway_instance
    ) {}

    public function userType(): UserType {
        return $this->user_type ?? $this->user_type = new UserType($this, $this->gateway_instance->repository_gateway);
    }

    public function repositoryType(): RepositoryType {
        return $this->repository_type ?? $this->repository_type = new RepositoryType($this);
    }

    public function fromLimitInputType(): FromLimitInputType {
        return $this->from_limit_type ?? $this->from_limit_type = new FromLimitInputType();
    }

}
