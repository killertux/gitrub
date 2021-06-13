<?php


namespace Gitrub\App\Web\Request;


class Request {

    public function __construct(
        public array $query,
        public ?string $body = null,
    ) {}

    public static function create(): self {
        return new self(
            query: $_GET,
            body: stream_get_contents(fopen('php://input', 'r'))
        );
    }

    public static function empty(): self {
        return new self([], null);
    }

}
