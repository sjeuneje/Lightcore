<?php

namespace Core\Exceptions;

class HttpException extends \Exception
{
    protected int $statusCode;

    public function __construct(
        string $message = "",
        int $statusCode = 500,
        int $code = 0, ?
        \Throwable $previous = null
    ) {
        $this->statusCode = $statusCode;
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
