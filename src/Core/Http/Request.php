<?php

namespace Core\Http;

use Exception;

/**
 * HTTP Request handler with lazy loading and normalized access
 *
 * Provides a clean interface for accessing HTTP request data including
 * headers, parameters, files, and body content. Uses lazy loading for
 * performance and handles various content types automatically.
 */
class Request
{
    /**
     * Valid HTTP methods
     *
     * @var array<string>
     */
    private const VALID_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Maximum request body size (5MB)
     *
     * @var int
     */
    private const MAX_BODY_SIZE = 5242880;

    /**
     * Server environment data
     *
     * @var array<string, mixed>
     */
    private array $server;

    /**
     * HTTP method
     *
     * @var string|null
     */
    private ?string $method = null;

    /**
     * Complete request URI
     *
     * @var string|null
     */
    private ?string $uri = null;

    /**
     * Request path without query string
     *
     * @var string|null
     */
    private ?string $path = null;

    /**
     * URL scheme http/https
     *
     * @var string|null
     */
    private ?string $scheme = null;

    /**
     * Query string
     *
     * @var string|null
     */
    private ?string $queryString = null;

    /**
     * Normalized headers
     *
     * @var array<string, string>|null
     */
    private ?array $headers = null;

    /**
     * Query parameters
     *
     * @var array<string, mixed>|null
     */
    private ?array $query = null;

    /**
     * Body parameters
     *
     * @var array<string, mixed>|null
     */
    private ?array $body = null;

    /**
     * Uploaded files
     *
     * @var array<string, array<string, mixed>>|null
     */
    private ?array $files = null;

    /**
     * Raw request body cache
     *
     * @var string|null
     */
    private ?string $rawBody = null;

    /**
     * Store route parameters for validation
     *
     * @var array<string, mixed>
     */
    private array $params = [];

    /**
     * Initialize request (usually from superglobals)
     *
     * @param array<string, mixed> $server Server environment ($_SERVER)
     */
    public function __construct(array $server = [])
    {
        $this->server = $server ?: $_SERVER;
    }

    /**
     * Get HTTP method
     *
     * @return string HTTP method (uppercase)
     */
    public function getMethod(): string
    {
        if ($this->method === null) {
            $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
            $this->method = in_array($method, self::VALID_METHODS) ? $method : 'GET';
        }

        return $this->method;
    }

    /**
     * Get complete request URI
     *
     * @return string Full URI with scheme, host, path and query
     */
    public function getUri(): string
    {
        if ($this->uri === null) {
            $scheme = $this->getScheme();
            $host = $this->server['HTTP_HOST'] ?? 'localhost';
            $uri = $this->server['REQUEST_URI'] ?? '/';

            $this->uri = "{$scheme}://{$host}{$uri}";
        }

        return $this->uri;
    }

    /**
     * Get request path without query string
     *
     * @return string Clean path (e.g., '/api/users')
     */
    public function getPath(): string
    {
        if ($this->path === null) {
            $uri = $this->server['REQUEST_URI'] ?? '/';
            $this->path = strtok($uri, '?') ?: '/';
        }

        return $this->path;
    }

    /**
     * Get URL scheme
     *
     * @return string 'http' or 'https'
     */
    public function getScheme(): string
    {
        if ($this->scheme === null) {
            $https = $this->server['HTTPS'] ?? '';
            $port = (int) ($this->server['SERVER_PORT'] ?? 80);

            $this->scheme = ($https === 'on' || $port === 443) ? 'https' : 'http';
        }

        return $this->scheme;
    }

    /**
     * Get query string
     *
     * @return string Raw query string
     */
    public function getQueryString(): string
    {
        if ($this->queryString === null) {
            $this->queryString = $this->server['QUERY_STRING'] ?? '';
        }

        return $this->queryString;
    }

    /**
     * Get header value by name (case-insensitive)
     *
     * @param string $name Header name
     * @param string $default Default value if header not found
     * @return string Header value or default
     */
    public function header(string $name, string $default = ''): string
    {
        $headers = $this->getAllHeaders();
        $normalized = strtolower($name);

        return $headers[$normalized] ?? $default;
    }

    /**
     * Get all headers
     *
     * @return array<string, string> All request headers (normalized keys)
     */
    public function headers(): array
    {
        return $this->getAllHeaders();
    }

    /**
     * Get query parameter value
     *
     * @param string $key Parameter name
     * @param mixed|null $default Default value if parameter not found
     * @return mixed Parameter value or default
     */
    public function query(string $key = null, mixed $default = null): mixed
    {
        $queryParams = $this->getAllQuery();

        if ($key === null) {
            return $queryParams;
        }

        return $queryParams[$key] ?? $default;
    }

    /**
     * Get a parameter by key, including route parameters
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }

        return $default;
    }

    /**
     * Get all input parameters (query + body merged)
     *
     * @return array<string, mixed> All input parameters
     */
    public function all(): array
    {
        return array_merge($this->getAllQuery(), $this->getAllBody());
    }

    /**
     * Get parsed JSON body as array
     *
     * @return array<string, mixed>|null Parsed JSON or null on failure
     */
    public function json(): ?array
    {
        if (!$this->isJsonContent()) {
            return null;
        }

        $body = $this->getRawBody();
        if (empty($body)) {
            return null;
        }

        $data = json_decode($body, true);
        return json_last_error() === JSON_ERROR_NONE ? $data : null;
    }

    /**
     * Get uploaded file data
     *
     * @param string $name Form field name
     * @return array<string, mixed>|null File data or null
     */
    public function file(string $name): ?array
    {
        $files = $this->getAllFiles();
        return $files[$name] ?? null;
    }

    /**
     * Check if request has uploaded file
     *
     * @param string $name Form field name
     * @return bool True if file exists and uploaded successfully
     */
    public function hasFile(string $name): bool
    {
        $file = $this->file($name);
        return $file !== null && ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;
    }

    /**
     * Check if request is HTTPS
     *
     * @return bool True if request is secure
     */
    public function isSecure(): bool
    {
        return $this->getScheme() === 'https';
    }

    /**
     * Check if request is AJAX
     *
     * @return bool True if X-Requested-With header indicates AJAX
     */
    public function isAjax(): bool
    {
        return strtolower($this->header('X-Requested-With')) === 'xmlhttprequest';
    }

    /**
     * Check if request method matches
     *
     * @param string $method Method to check (case-insensitive)
     * @return bool True if methods match
     */
    public function isMethod(string $method): bool
    {
        return strtoupper($method) === $this->getMethod();
    }

    /**
     * Check if request expects JSON response
     *
     * @return bool True if Accept header indicates JSON preference
     */
    public function expectsJson(): bool
    {
        $accept = $this->header('Accept');
        return str_contains($accept, 'application/json') || $this->isAjax();
    }

    /**
     * Get client IP address
     *
     * @return string Client IP address
     */
    public function ip(): string
    {
        // Check proxy headers first
        $proxyHeaders = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP'
        ];

        foreach ($proxyHeaders as $header) {
            if (!empty($this->server[$header])) {
                $ips = explode(',', (string) $this->server[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Get user agent string
     *
     * @return string User agent or empty string
     */
    public function userAgent(): string
    {
        return $this->header('User-Agent');
    }

    /**
     * Parse and cache all headers
     *
     * @return array<string, string> Normalized headers array
     */
    private function getAllHeaders(): array
    {
        if ($this->headers === null) {
            $this->headers = [];

            foreach ($this->server as $key => $value) {
                if (str_starts_with($key, 'HTTP_')) {
                    $name = substr($key, 5);
                    $name = str_replace('_', '-', strtolower($name));
                    $this->headers[$name] = (string) $value;
                } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
                    $name = str_replace('_', '-', strtolower($key));
                    $this->headers[$name] = (string) $value;
                }
            }
        }

        return $this->headers;
    }

    /**
     * Parse and cache query parameters
     *
     * @return array<string, mixed> Query parameters array
     */
    private function getAllQuery(): array
    {
        if ($this->query === null) {
            $this->query = $_GET;
        }

        return $this->query;
    }

    /**
     * Parse and cache body parameters
     *
     * @return array<string, mixed> Body parameters array
     */
    private function getAllBody(): array
    {
        if ($this->body === null) {
            $this->body = $this->parseBodyContent();
        }

        return $this->body;
    }

    /**
     * Parse and cache uploaded files
     *
     * @return array<string, array<string, mixed>> Files array
     */
    private function getAllFiles(): array
    {
        if ($this->files === null) {
            $this->files = $_FILES;
        }

        return $this->files;
    }

    /**
     * Parse body content based on content type
     *
     * @return array<string, mixed> Parsed body data
     */
    private function parseBodyContent(): array
    {
        $method = $this->getMethod();

        // For GET/HEAD, body is always empty
        if (in_array($method, ['GET', 'HEAD'])) {
            return [];
        }

        // For POST with form content type, use $_POST
        if ($method === 'POST' && $this->isFormContent()) {
            return $_POST;
        }

        // For other methods or content types, parse raw body
        $rawBody = $this->getRawBody();
        if (empty($rawBody)) {
            return [];
        }

        return match (true) {
            $this->isJsonContent() => $this->parseJsonBody($rawBody),
            $this->isFormContent() => $this->parseFormBody($rawBody),
            default => []
        };
    }

    /**
     * Get raw request body with size limit
     *
     * @return string Raw body content
     */
    private function getRawBody(): string
    {
        if ($this->rawBody === null) {
            $input = file_get_contents('php://input');

            if ($input === false) {
                $this->rawBody = '';
            } elseif (strlen($input) > self::MAX_BODY_SIZE) {
                throw new \RuntimeException('Request body too large');
            } else {
                $this->rawBody = $input;
            }
        }

        return $this->rawBody;
    }

    /**
     * Parse JSON body content
     *
     * @param string $body Raw body content
     * @return array<string, mixed> Parsed JSON data
     */
    private function parseJsonBody(string $body): array
    {
        $data = json_decode($body, true);
        return (json_last_error() === JSON_ERROR_NONE && is_array($data)) ? $data : [];
    }

    /**
     * Parse form-encoded body content
     *
     * @param string $body Raw body content
     * @return array<string, mixed> Parsed form data
     */
    private function parseFormBody(string $body): array
    {
        $params = [];
        parse_str($body, $params);
        return $params;
    }

    /**
     * Check if content type is JSON
     *
     * @return bool True if content is JSON
     */
    private function isJsonContent(): bool
    {
        $contentType = $this->header('Content-Type');
        return str_contains($contentType, 'application/json');
    }

    /**
     * Check if content type is form data
     *
     * @return bool True if content is form data
     */
    private function isFormContent(): bool
    {
        $contentType = $this->header('Content-Type');
        return str_contains($contentType, 'application/x-www-form-urlencoded');
    }

    /**
     * Get POST/body parameter value
     *
     * @param string|null $key Parameter name
     * @param mixed|null $default Default value if parameter not found
     * @return mixed Parameter value or all parameters if key is null
     */
    public function post(string $key = null, mixed $default = null): mixed
    {
        $bodyParams = $this->getAllBody();

        if ($key === null) {
            return $bodyParams;
        }

        return $bodyParams[$key] ?? $default;
    }

    /**
     * Get uploaded file information
     *
     * @param string|null $key File field name
     * @return mixed File info or all files if key is null
     */
    public function files(string $key = null): mixed
    {
        $filesData = $this->getAllFiles();

        if ($key === null) {
            return $filesData;
        }

        return $filesData[$key] ?? null;
    }

    /**
     * Set parameters extracted from the route
     *
     * @param array<string, mixed> $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * Validate request parameters against provided rules.
     *
     * @param array<string, string> $rules Key-value pairs of parameter names and validation rules
     * @throws Exception if validation fails
     */
    public function validate(array $rules): void
    {
        foreach ($rules as $key => $rule) {
            $value = $this->input($key) ?? $this->post($key);

            $rulesArray = explode('|', $rule);
            foreach ($rulesArray as $currentRule) {
                switch ($currentRule) {
                    case 'required':
                        if (is_null($value) || $value === '') {
                            throw new Exception("The parameter '$key' is required.");
                        }
                        break;

                    case 'string':
                        if (!is_string($value)) {
                            throw new Exception("The parameter '$key' must be a string.");
                        }
                        break;

                    case 'integer':
                        if (!is_int($value)) {
                            $valueAsInt = filter_var($value, FILTER_VALIDATE_INT);
                            if ($valueAsInt === false) {
                                throw new Exception("The parameter '$key' must be an integer.");
                            }
                        }
                        break;

                    default:
                        throw new Exception("Unknown validation rule: '$currentRule' for parameter '$key'.");
                }
            }
        }
    }
}
