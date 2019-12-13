<?php

namespace Nullform\ZeroBounce\Http;

/**
 * ZeroBounce API response.
 *
 * @package Nullform\ZeroBounce
 */
class Response
{
    /**
     * HTTP status code.
     *
     * @var int
     */
    protected $http_status = 0;

    /**
     * Response body.
     *
     * @var string
     */
    protected $body = '';

    /**
     * Restored from cache.
     *
     * @var bool
     */
    protected $from_cache = false;


    /**
     * @param string $body Response body.
     */
    public function __construct(string $body = '')
    {
        if (!empty($body)) {
            $this->setBody($body);
        }
    }

    /**
     * Response restored from cache.
     * Get or set value.
     *
     * @param bool|null $value New value if needed.
     * @return bool
     */
    public function fromCache(?bool $value = null): bool
    {
        if (!is_null($value)) {
            $this->from_cache = $value;
        }

        return $this->from_cache;
    }

    /**
     * Get HTTP status code.
     *
     * @return int
     */
    public function getHttpStatus(): int
    {
        return $this->http_status;
    }

    /**
     * Decoded payload from response body.
     *
     * @return \stdClass|array|string|null
     * @uses json_decode()
     */
    public function getPayload()
    {
        return @json_decode($this->body);
    }

    /**
     * Get raw response body.
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Set HTTP status code.
     *
     * @param int $http_status
     */
    public function setHttpStatus(int $http_status): void
    {
        $this->http_status = $http_status;
    }

    /**
     * Set response body.
     *
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }
}