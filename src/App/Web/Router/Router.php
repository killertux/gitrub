<?php


namespace Gitrub\App\Web\Router;


use Gitrub\App\Web\Response\Response;
use Gitrub\App\Web\Response\ResponseHandler;
use Steampixel\Route;

class Router {

    private array $exception_presenters = [];

    public function __construct(
        private ResponseHandler $response_handler,
    ) {}

    public function addRoute(string $expression, callable $closure, string $method = 'GET'): self {
        Route::add(
            $expression,
            $this->handler($closure),
            $method
        );
        return $this;
    }

    public function addExceptionPresenter(string $class, string $presenter_class): self {
        $this->exception_presenters[$class] = $presenter_class;
        return $this;
    }

    public function run(): void {
        $this->handlePathAndMethodNotFound();
        Route::run('', true, false, true);
    }

    private function handlePathAndMethodNotFound(): void {
        Route::pathNotFound(function (string $path) {
            $this->response_handler->handle(
                new Response(
                    http_code: 404,
                    body: json_encode(['error' => "Can not execute $path"])
                )
            );
        });

        Route::methodNotAllowed(function (string $path, string $method) {
            $this->response_handler->handle(
                new Response(
                    http_code: 405,
                    body: json_encode(['error' => "Can not execute $path with method $method"])
                )
            );
        });
    }

    private function handler(callable $closure): callable {
        return function (...$params) use ($closure) {
            try {
                $response = $closure(...$params);
            } catch (\Exception $exception) {
                if ($this->exception_presenters[$exception::class]) {
                    $response = new $this->exception_presenters[$exception::class]($exception);
                } else {
                    throw $exception;
                }
            } catch (\Throwable $t) {
                $response = new Response(
                    http_code: 500,
                    body: json_encode(["error" => "Internal server error"])
                );
            }
            $this->response_handler->handle($response);
        };
    }
}
