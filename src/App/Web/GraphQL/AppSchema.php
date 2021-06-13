<?php


namespace Gitrub\App\Web\GraphQL;

use Gitrub\App\GatewayInstances;
use Gitrub\App\Web\GraphQL\Type\MutationType;
use Gitrub\App\Web\GraphQL\Type\QueryType;
use Gitrub\App\Web\GraphQL\Type\TypeRegistry;
use GraphQL\Type\Schema;

class AppSchema extends Schema{

   public function __construct(GatewayInstances $gateway_instances) {
       $type_registry = new TypeRegistry($gateway_instances);
       parent::__construct([
            'query' => new QueryType($type_registry, $gateway_instances),
            'mutation' => new MutationType($type_registry, $gateway_instances),
       ]);
   }
}
