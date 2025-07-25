<?php

namespace Core\Http;

use RuntimeException;

/**
 * HTTP Response class
 *
 * Handles HTTP responses with fluent interface and factory methods
 *
 * @author Your Name
 */
class Response
{
    /**
     * HTTP Status Code Constants
    */
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_FOUND = 302;
    public const HTTP_NOT_MODIFIED = 304;
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_BAD_GATEWAY = 502;
    public const HTTP_SERVICE_UNAVAILABLE = 503;

    /**
     * The content of the Response
     *
     * @var string
     */
    private string $content = '';

    /**
     * Response status code, default 200
     *
     * @var int
     */
    private int $statusCode = self::HTTP_OK;

    /**
     * The headers of the response
     *
     * @var array
     */
    private array $headers = [];

    /**
     * Create a new Response instance
     *
     * @param string $content Response content
     * @param int $statusCode HTTP status code
     * @param array $headers HTTP headers
     */
    public function __construct(string $content = '', int $statusCode = self::HTTP_OK, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Create a JSON response
     *
     * @param array $data Data to encode as JSON
     * @param int $statusCode HTTP status code
     * @return self
     * @throws RuntimeException If JSON encoding fails
     */
    public static function json(array $data, int $statusCode = self::HTTP_OK): self
    {
        try {
            $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        } catch (\JsonException $e) {
            throw new RuntimeException('Failed to encode JSON: ' . $e->getMessage(), 0, $e);
        }

        return new self($json, $statusCode, ['Content-Type' => 'application/json; charset=utf-8']);
    }

    /**
     * Create an HTML response
     *
     * @param string $content HTML content
     * @param int $statusCode HTTP status code
     * @return self
     */
    public static function html(string $content, int $statusCode = self::HTTP_OK): self
    {
        return new self($content, $statusCode, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    /**
     * Create a redirect response
     *
     * @param string $url Redirect URL
     * @param int $statusCode HTTP status code (302 by default)
     * @return self
     */
    public static function redirect(string $url, int $statusCode = self::HTTP_FOUND): self
    {
        return new self('', $statusCode, ['Location' => $url]);
    }

    /**
     * Create a 404 Not Found response
     *
     * @param string $message Error message
     * @return self
     */
    public static function notFound(string $message = 'Not Found'): self
    {
        return self::json(['error' => $message], self::HTTP_NOT_FOUND);
    }

    /**
     * Create an error response
     *
     * @param string $message Error message
     * @param int $statusCode HTTP status code (500 by default)
     * @return self
     */
    public static function error(string $message, int $statusCode = self::HTTP_INTERNAL_SERVER_ERROR): self
    {
        return self::json(['error' => $message], $statusCode);
    }

    /**
     * Create a plain text response
     *
     * @param string $content Text content
     * @param int $statusCode HTTP status code
     * @return self
     */
    public static function text(string $content, int $statusCode = self::HTTP_OK): self
    {
        return new self($content, $statusCode, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    /**
     * Create an XML response
     *
     * @param string $content XML content
     * @param int $statusCode HTTP status code
     * @return self
     */
    public static function xml(string $content, int $statusCode = self::HTTP_OK): self
    {
        return new self($content, $statusCode, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    /**
     * Set the response content
     *
     * @param string $content Response content
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set the HTTP status code
     *
     * @param int $statusCode HTTP status code
     * @return self
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Set a header
     *
     * @param string $name Header name
     * @param string $value Header value
     * @return self
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Set multiple headers
     *
     * @param array $headers Associative array of headers
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * Remove a header
     *
     * @param string $name Header name
     * @return self
     */
    public function removeHeader(string $name): self
    {
        unset($this->headers[$name]);
        return $this;
    }

    /**
     * Check if a header exists
     *
     * @param string $name Header name
     * @return bool
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$name]);
    }

    /**
     * Get a specific header value
     *
     * @param string $name Header name
     * @param string|null $default Default value if header doesn't exist
     * @return string|null
     */
    public function getHeader(string $name, ?string $default = null): ?string
    {
        return $this->headers[$name] ?? $default;
    }

    /**
     * Get the response content
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Get the HTTP status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get all headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get the content length
     *
     * @return int
     */
    public function getContentLength(): int
    {
        return strlen($this->content);
    }

    /**
     * Check if the response is successful (2xx status codes)
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Check if the response is a redirect (3xx status codes)
     *
     * @return bool
     */
    public function isRedirect(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * Check if the response is an error (4xx or 5xx status codes)
     *
     * @return bool
     */
    public function isError(): bool
    {
        return $this->statusCode >= 400;
    }

    /**
     * Send the response to the browser
     *
     * @return Response
     * @throws RuntimeException If headers are already sent
     */
    public function send(): Response
    {
        if (headers_sent($file, $line)) {
            throw new RuntimeException("Headers already sent in $file on line $line");
        }

        // Set status code
        http_response_code($this->statusCode);

        // Set Content-Length header automatically
        if (!$this->hasHeader('Content-Length') && $this->content !== '') {
            $this->setHeader('Content-Length', (string) $this->getContentLength());
        }

        // Send headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Send content
        echo $this->content;

        return $this;
    }

    /**
     * Convert response to string (useful for debugging)
     *
     * @return string
     */
    public function __toString(): string
    {
        $headers = [];
        foreach ($this->headers as $name => $value) {
            $headers[] = "$name: $value";
        }

        return sprintf(
            "HTTP/1.1 %d\n%s\n\n%s",
            $this->statusCode,
            implode("\n", $headers),
            $this->content
        );
    }
}
