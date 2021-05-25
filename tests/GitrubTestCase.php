<?php

namespace Test\Gitrub;

use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Test\Gitrub\Support\GatewayInstancesProvider;
use Test\Gitrub\Support\RepositoryProvider;
use Test\Gitrub\Support\UserProvider;

class GitrubTestCase extends TestCase {

	/** @var Generator|UserProvider|RepositoryProvider|GatewayInstancesProvider  */
	protected Generator $faker;

	public function __construct(?string $name = null, array $data = [], $dataName = '') {
		parent::__construct($name, $data, $dataName);
		$this->faker = \Faker\Factory::create();
		$this->faker->addProvider(new UserProvider($this->faker));
		$this->faker->addProvider(new RepositoryProvider($this->faker));
		$this->faker->addProvider(new GatewayInstancesProvider($this->faker));
	}
}
