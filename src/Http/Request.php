<?php

namespace Nullform\ZeroBounce\Http;

use Nullform\ZeroBounce\Exceptions\HttpException;
use Nullform\ZeroBounce\Params;
use Nullform\ZeroBounce\Params\AbstractParams;

/**
 * ZeroBounce API request.
 *
 * @package Nullform\ZeroBounce
 */
class Request
{
    const USER_AGENT = 'nullform/zerobounce-client';

    /**
     * GET or POST.
     *
     * @var string
     */
    public $method;

    /**
     * Base URL.
     *
     * @var string
     */
    public $base_url;

    /**
     * Request path.
     *
     * Example: validate
     *
     * @var string
     */
    public $path;

    /**
     * Request parameters.
     *
     * @var Params\AbstractParams
     */
    public $params;

    /**
     * Request timeout.
     *
     * @var int
     * @see Client::timeout()
     */
    public $timeout = 0;

    /**
     * Path to the file which will be uploaded.
     *
     * @var string|null
     */
    public $filename;

    /**
     * Request body.
     *
     * @var string
     */
    public $body = '';


    /**
     * 32 byte hash of Request object.
     *
     * @return string
     */
    public function hash(): string
    {
        return md5(json_encode($this));
    }

    /**
     * Send HTTP request.
     *
     * @return Response
     * @throws HttpException
     * @uses curl_exec()
     */
    public function send(): Response
    {
        $response = new Response();
        $response_body = '';
        $params_string = $this->params instanceof AbstractParams ? $this->params->toString() : '';
        $endpoint = $this->base_url . '/' . $this->path;

        $curl_options = [
            CURLOPT_URL            => $endpoint . (!empty($params_string) ? '?' . $params_string : ''),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_USERAGENT      => static::USER_AGENT,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        ];

        if ($this->method === 'POST') {
            $curl_options[CURLOPT_POST] = true;
        }

        if (!is_null($this->filename)) { // Upload a file

            if (!is_file($this->filename) || !is_readable($this->filename)) {
                throw new HttpException('Can\'t read the input file');
            }

            $filename = realpath($this->filename);
            $mimetype = mime_content_type($filename);

            if (preg_match('/.csv$/i', $filename)) {
                $mimetype = 'text/csv';
            }

            $curl_options[CURLOPT_POST] = true;
            $curl_options[CURLOPT_URL] = $endpoint;
            $curl_options[CURLOPT_POSTFIELDS] = array_merge(
                [
                    'file' => curl_file_create(
                        $filename,
                        $mimetype,
                        basename($filename)
                    )
                ],
                $this->params->toArray()
            );

        }

        $ch = curl_init();

        curl_setopt_array($ch, $curl_options);

        $response_body = (string)curl_exec($ch);

        $curl_info = curl_getinfo($ch);

        $response->setBody($response_body);
        $response->setHttpStatus((int)$curl_info['http_code']);

        if (!$response->getHttpStatus()) {
            throw new HttpException('ZeroBounce server not responding');
        }

        return $response;
    }
}