<?php


namespace Gitrub\App\Web\GraphQL\Type;


use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class FromLimitInputType extends InputObjectType {

    public function __construct() {
        parent::__construct([
            'name' => 'FromLimit',
            'fields' => [
                'from' => [
                    'type' => Type::int(),
                    'defaultValue' => 0,
                ],
                'limit' => [
                    'type' => Type::int(),
                    'defaultValue' => 50,
                ],
            ],
        ]);
    }
}
