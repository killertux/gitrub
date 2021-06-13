<?php

namespace Test\Gitrub\App\Web\GraphQL;

use Gitrub\App\GatewayInstances;
use Gitrub\App\Web\GraphQL\AppSchema;
use PHPUnit\Framework\TestCase;
use Test\Gitrub\GitrubTestCase;

class AppSchemaTest extends GitrubTestCase {

    /**
     * @doesNotPerformAssertions
     */
    public function testIsValid(): void {
        (new AppSchema($this->faker->gatewayInstances()))->assertValid();
    }
}
