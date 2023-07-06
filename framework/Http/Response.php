<?php

namespace Lightcore\Framework\Http;

class Response
{
    public function __construct(
        private ?string $content = '',
        private int $status = 200,
        private array $headers = []
    )
    {}
}