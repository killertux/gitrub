<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new \Gitrub\App\EnvLoader())->load();
(new \Gitrub\App\Web\WebApp(
	\Gitrub\App\GatewayInstances::default(),
	new \Gitrub\App\Web\Rest\Response\EchoResponseHandler(),
))->run();
