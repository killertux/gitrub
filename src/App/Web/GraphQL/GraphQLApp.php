<?php


namespace Gitrub\App\Web\GraphQL;


use Gitrub\App\GatewayInstances;
use Gitrub\App\Web\Request\Request;
use Gitrub\App\Web\Response\Response;
use Gitrub\App\Web\Router\Router;
use GraphQL\GraphQL;

class GraphQLApp {

    public function __construct(
        private Router $router,
        private GatewayInstances $gateway_instances,
    ) {}

    public function setup(): void {
        $this->router->addRoute('/graphql', function (Request $request) {
            $schema = new AppSchema($this->gateway_instances);
            $input = json_decode($request->body, true);
            $result = GraphQL::executeQuery(schema: $schema, source: $input['query'], variableValues:  $input['variables'] ?? null);
            return new Response(
                http_code: 200,
                body: json_encode($result->toArray()),
            );
        }, 'POST');
    }

}
