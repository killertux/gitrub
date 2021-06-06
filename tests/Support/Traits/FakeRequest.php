<?php


namespace Test\Gitrub\Support\Traits;


use Gitrub\App\Web\WebApp;

trait FakeRequest {

    use ServerGlobalCleaner;
    use GetGlobalCleaner;

    private function fakeRequest(WebApp $web_app, string $uri, string $method): void {
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REQUEST_METHOD'] = $method;
        $web_app->run();
    }
}
