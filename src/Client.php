<?php

namespace Nullform\ZeroBounce;

use Nullform\ZeroBounce\Exceptions\AbstractException;
use Nullform\ZeroBounce\Exceptions\CacheException;
use Nullform\ZeroBounce\Exceptions\HttpException;
use Nullform\ZeroBounce\Exceptions\ParamsException;
use Nullform\ZeroBounce\Exceptions\ZeroBounceException;
use Nullform\ZeroBounce\Http\Request;
use Nullform\ZeroBounce\Http\Response;
use Nullform\ZeroBounce\Models\Email;
use Nullform\ZeroBounce\Models\BulkFile;
use Nullform\ZeroBounce\Models\Usage;
use Nullform\ZeroBounce\Params\AbstractParams;
use Nullform\ZeroBounce\Params\BulkSendFileParams;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * ZeroBounce API client.
 *
 * @package Nullform\ZeroBounce
 * @see     https://www.zerobounce.net/docs/email-validation-api-quickstart/
 */
class Client
{
    public const API_URL = 'https://api.zerobounce.net';
    public const BULK_API_URL = 'https://bulkapi.zerobounce.net';
    public const DEFAULT_TIMEOUT = 10;

    /**
     * API version.
     *
     * @var string
     */
    protected $version = '2';

    /**
     * Last request.
     *
     * @var Request|null
     */
    protected $request;

    /**
     * Last response.
     *
     * @var Response|null
     */
    protected $response;

    /**
     * PSR-6 or PSR-16 cache instance.
     *
     * @var CacheItemPoolInterface|CacheInterface|null
     */
    protected $cache;

    /**
     * Cache TTL.
     *
     * @var int
     */
    protected $cache_ttl = 60;

    /**
     * Prefix for cache items.
     *
     * @var string
     */
    protected $cache_prefix = 'zerobounce_client_';

    /**
     * ZeroBounce API key.
     *
     * @var string
     */
    protected $api_key = '';

    /**
     * Request timeout.
     *
     * @var int
     */
    protected $timeout = self::DEFAULT_TIMEOUT;

    /**
     * The function for logging that will be executed after every API call.
     *
     * @var callable
     */
    protected $log_function;


    /**
     * @param string $api_key
     */
    public function __construct(string $api_key)
    {
        $this->api_key = $api_key;

        $this->request = new Request();
        $this->response = new Response();
    }

    /**
     * Get current API version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Timeout (maximum time the request is allowed to take).
     * Get or set value.
     *
     * @param int|null $timeout New value if needed.
     * @return int Current value.
     */
    public function timeout(?int $timeout = null): int
    {
        if (!is_null($timeout)) {
            $this->timeout = $timeout;
        }

        return $this->timeout;
    }

    /**
     * Caching of API responses.
     *
     * You can pass null to disable caching.
     *
     * @param CacheItemPoolInterface|CacheInterface|null $cache PSR-6 or PSR-16 cache instance.
     * @param int                                        $ttl
     * @param string                                     $prefix
     * @return bool Is caching currently available (cache instance successfully set).
     */
    public function caching($cache, int $ttl, string $prefix = ''): bool
    {
        if ($cache instanceof CacheItemPoolInterface || $cache instanceof CacheInterface) {
            $this->cache = $cache;
        } else {
            $this->cache = null;
        }

        $this->cache_ttl = $ttl;
        $this->cache_prefix = $prefix;

        return !is_null($this->cache) ? true : false;
    }

    /**
     * The function for logging that will be called after every ZeroBounce API call.
     *
     * The function takes a client instance as a parameter.
     *
     * @param callable|null $func
     * @return bool Function is set.
     */
    public function logFunction(?callable $func): bool
    {
        $this->log_function = $func;

        return !empty($this->log_function);
    }

    /**
     * Get last request.
     *
     * @return Request
     */
    public function lastRequest(): Request
    {
        return $this->request;
    }

    /**
     * Get last response.
     *
     * @return Response
     */
    public function lastResponse(): Response
    {
        return $this->response;
    }

    /**
     * Get credit balance.
     *
     * @return int
     * @throws HttpException
     * @throws CacheException
     * @throws ParamsException
     * @see https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-credit-balance/ Validation API: Credit Balance
     */
    public function getCredits(): int
    {
        $response = $this->call('GET', $this->apiBaseUrl(), 'getcredits');
        $payload = $response->getPayload();
        $credits = -1;

        $credits = (int)$payload->Credits;

        return $credits;
    }

    /**
     * Validate email.
     *
     * @param string      $email      The email address you want to validate.
     * @param string|null $ip_address The IP Address the email signed up from.
     * @return Email
     * @throws CacheException
     * @throws HttpException
     * @throws ZeroBounceException
     * @throws ParamsException
     * @see https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-validate-emails/ Validation API: Validate Emails
     */
    public function validate(string $email, ?string $ip_address = null): Email
    {
        $params = new class extends AbstractParams {
            /**
             * @var string
             */
            public $email;
            /**
             * @var string
             */
            public $ip_address;
        };

        $params->email = $email;
        $params->ip_address = (string)$ip_address;

        $params->checkRequiredParams(['email']);

        $response = $this->call('GET', $this->apiBaseUrl(), 'validate', $params);

        $payload = $response->getPayload();

        if (!empty($payload->error)) {
            throw new ZeroBounceException($payload->error);
        }

        return new Models\Email($payload);
    }

    /**
     * Get API usage.
     *
     * $start_date and $end_date - timestamp or string that the strtotime(), DateTime and date_create() parser
     * understands.
     *
     * @param string|int $start_date
     * @param string|int $end_date
     * @return Usage
     * @throws CacheException
     * @throws HttpException
     * @throws ParamsException
     * @see https://www.php.net/manual/en/datetime.formats.php Supported Date and Time Formats
     * @see https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-get-api-usage/ Validation API: API Usage
     */
    public function getUsage($start_date, $end_date = 'now'): Usage
    {
        $params = new class extends AbstractParams {
            /**
             * @var string
             */
            public $start_date;

            /**
             * @var string
             */
            public $end_date;
        };

        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);

        if (empty($start_ts) || empty($end_ts)) {
            throw new ParamsException('Period not specified');
        }

        $params->start_date = date("Y-m-d", $start_ts);
        $params->end_date = date("Y-m-d", $end_ts);

        $response = $this->call('GET', $this->apiBaseUrl(), 'getapiusage', $params);

        return new Usage($response->getPayload());
    }

    /**
     * Send a csv or txt file for bulk email validation/scoring.
     *
     * @param string             $filename Path to the csv file which will be uploaded.
     * @param string             $type     Type: validation | scoring.
     * @param BulkSendFileParams $params
     * @return BulkFile
     * @throws CacheException
     * @throws HttpException
     * @throws ParamsException
     * @throws ZeroBounceException
     * @see https://www.zerobounce.net/docs/email-list-validation/#Allowed_File_Formats Allowed File Formats
     * @see https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-send-file/ Validation API: Send File
     * @see https://www.zerobounce.net/docs/ai-scoring-api/send-file/ AI Scoring API: Send File
     * @see BulkFile::TYPE_VALIDATION
     * @see BulkFile::TYPE_SCORING
     */
    public function bulkSendFile(string $filename, string $type, BulkSendFileParams $params): BulkFile
    {
        $params->checkRequiredParams(['email_address_column']);

        $path = 'sendfile';

        if ($type == BulkFile::TYPE_SCORING) {
            $path = 'scoring/sendfile';
        }

        $response = $this->call(
            'POST',
            $this->bulkApiBaseUrl(),
            $path,
            $params,
            $filename
        );

        $payload = $response->getPayload();

        if (empty($payload->success)) {
            $error = 'Unknown error while uploading the file';
            if (!empty($payload->message)) {
                if (is_array($payload->message)) {
                    $error = implode('. ', $payload->message);
                } else {
                    $error = (string)$payload->message;
                }
            }
            throw new ZeroBounceException($error);
        }

        $file_id = $payload->file_id;
        $file_info = new BulkFile($payload);

        try {
            $file_info = $this->bulkFileStatus($file_id, $type);
        } catch (AbstractException $exception) {
        }

        return $file_info;
    }

    /**
     * Uploaded file status.
     *
     * @param string $file_id
     * @param string $type Type: validation | scoring.
     * @return BulkFile
     * @throws CacheException
     * @throws HttpException
     * @throws ParamsException
     * @throws ZeroBounceException
     * @see https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-file-status/ Validation API: File Status
     * @see https://www.zerobounce.net/docs/ai-scoring-api/file-status/ AI Scoring API: File Status
     * @see BulkFile::TYPE_VALIDATION
     * @see BulkFile::TYPE_SCORING
     */
    public function bulkFileStatus(string $file_id, string $type): BulkFile
    {
        $params = new class extends AbstractParams {
            public $file_id;
        };

        if (!empty($file_id)) {
            $params->file_id = $file_id;
        }

        $path = 'filestatus';

        if ($type == BulkFile::TYPE_SCORING) {
            $path = 'scoring/filestatus';
        }

        $params->checkRequiredParams(['file_id']);

        $response = $this->call('GET', $this->bulkApiBaseUrl(), $path, $params);
        $payload = $response->getPayload();

        if (empty($payload->success)) {
            throw new ZeroBounceException(
                !empty($payload->message) ? $payload->message : 'Unknown error while retrieving file info'
            );
        }

        return new BulkFile($payload);
    }

    /**
     * Get the validation/scoring results csv (or zip) file for the file been submitted using sendfile API.
     *
     * @param string      $file_id
     * @param string      $type            Type: validation | scoring.
     * @param string|null $output_filename Write file to the output buffer with this filename.
     * @return string File contents.
     * @throws CacheException
     * @throws HttpException
     * @throws ParamsException
     * @throws ZeroBounceException
     * @see https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-get-file/ Validation API: Get File
     * @see https://www.zerobounce.net/docs/ai-scoring-api/get-file/ AI Scoring API: Get File
     * @see BulkFile::TYPE_VALIDATION
     * @see BulkFile::TYPE_SCORING
     */
    public function bulkGetFile(string $file_id, string $type, ?string $output_filename = null): string
    {
        $params = new class extends AbstractParams {
            public $file_id;
        };

        if (!empty($file_id)) {
            $params->file_id = $file_id;
        }

        $params->checkRequiredParams(['file_id']);

        $path = 'getfile';

        if ($type == BulkFile::TYPE_SCORING) {
            $path = 'scoring/getfile';
        }

        $file = $this->bulkFileStatus($file_id, $type);

        if ($file->file_status != BulkFile::STATUS_COMPLETE) {
            throw new ZeroBounceException('Result file is incomplete. ' . $file->file_status);
        }

        $response = $this->call('GET', $this->bulkApiBaseUrl(), $path, $params);

        $content = $response->getBody();
        $payload = $response->getPayload();

        if (is_object($payload)) {
            throw new ZeroBounceException(
                !empty($payload->message) ? $payload->message : 'Unknown error while receiving file'
            );
        }

        if (empty($content)) {
            throw new ZeroBounceException('Result file is empty');
        }

        if ($output_filename) {

            $output_filename = preg_replace('/[^-_\w\d.]+/', '', $output_filename);
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimetype = $finfo->buffer($content);
            $ext = 'csv';

            if ($mimetype == 'application/zip') {
                $ext = 'zip';
            } else {
                $mimetype = 'text/csv';
            }

            header('Content-Type: ' . $mimetype);
            header('Content-Disposition: attachment; filename=' . $output_filename . '.' . $ext);
            header('Expires: -1');
            header('Cache-Control: no-cache');
            header('Pragma: no-cache');
            header('Content-Length: ' . strlen($content));

            echo $content;

        }

        return $content;
    }

    /**
     * Delete the file that was submitted using sendfile API.
     * File can be deleted only when its status is Complete.
     *
     * @param string $file_id
     * @param string $type Type: validation | scoring.
     * @return bool
     * @throws CacheException
     * @throws HttpException
     * @throws ParamsException
     * @throws ZeroBounceException
     * @see https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-delete-file/ Validation API: Delete File
     * @see https://www.zerobounce.net/docs/ai-scoring-api/delete-file/ AI Scoring API: Delete File
     * @see BulkFile::TYPE_VALIDATION
     * @see BulkFile::TYPE_SCORING
     */
    public function bulkDeleteFile(string $file_id, string $type): bool
    {
        $params = new class extends AbstractParams {
            public $file_id;
        };

        if (!empty($file_id)) {
            $params->file_id = $file_id;
        }

        $params->checkRequiredParams(['file_id']);

        $path = 'deletefile';

        if ($type == BulkFile::TYPE_SCORING) {
            $path = 'scoring/deletefile';
        }

        $response = $this->call('GET', $this->bulkApiBaseUrl(), $path, $params);

        $payload = $response->getPayload();

        if (empty($payload->success)) {
            throw new ZeroBounceException(
                !empty($payload->message) ? $payload->message : 'Unknown error while deleting file'
            );
        }

        return true;
    }

    /**
     * Calling an API method.
     *
     * @param string              $http_method GET or POST.
     * @param string              $base_url    Base URL.
     * @param string              $path        Example: validate.
     * @param AbstractParams|null $params
     * @param string|null         $filename    Path to the file which will be uploaded.
     * @return Response
     * @throws HttpException
     * @throws CacheException
     * @throws ParamsException
     */
    protected function call(
        string $http_method,
        string $base_url,
        string $path,
        ?AbstractParams $params = null,
        ?string $filename = null
    ): Response
    {
        if (strpos($path, '/') === 0) {
            $path = substr($path, 1);
        }

        if (is_null($params)) {
            $params = new class extends AbstractParams {
                public $api_key = '';
            };
        }

        $params->api_key = $this->api_key;

        if (empty($params->api_key)) {
            throw new ParamsException('No API key found');
        }

        // Make a new request...
        $this->request = new Request();
        $this->request->base_url = $base_url;
        $this->request->method = strtoupper($http_method);
        $this->request->path = $path;
        $this->request->params = $params;
        $this->request->timeout = $this->timeout();
        $this->request->filename = $filename;

        $cache_key = $this->cache_prefix . $this->request->hash();

        if (!is_null($this->cache)) {
            $cached_response = $this->getResponseFromCache($cache_key);
            if (!empty($cached_response) && $cached_response instanceof Response) {
                // Get response from cache
                $this->response = $cached_response;
                $this->response->fromCache(true);
                $this->log();
                return $this->response;
            }
        }

        $this->response = $this->request->send();

        $this->log();

        if (!is_null($this->cache) && $this->response->getHttpStatus() && $this->response->getHttpStatus() < 300) {
            $this->storeResponseToCache($cache_key);
        }

        return $this->response;
    }

    /**
     * Write ZeroBounce API calls to log.
     *
     * The method can be overridden in your application to log any API calls.
     *
     * @return mixed
     */
    protected function log()
    {
        return is_callable($this->log_function) ? call_user_func($this->log_function, $this) : null;
    }

    /**
     * Store response to cache.
     *
     * @param string $key
     * @return bool
     * @throws CacheException
     */
    protected function storeResponseToCache(string $key): bool
    {
        $stored = false;

        try {
            if ($this->cache instanceof CacheItemPoolInterface) { // PSR-6
                $item = $this->cache->getItem($key);
                $item->set($this->response);
                $item->expiresAfter($this->cache_ttl);

                $stored = $this->cache->save($item);
            } elseif ($this->cache instanceof CacheInterface) { // PSR-16
                $stored = $this->cache->set($key, $this->response, $this->cache_ttl);
            }
        } catch (\Psr\Cache\CacheException | \Psr\SimpleCache\CacheException $exception) {
            throw new CacheException($exception->getMessage());
        }

        return $stored;
    }

    /**
     * Get response from cache.
     *
     * @param string $key
     * @return Response|null
     * @throws CacheException
     */
    protected function getResponseFromCache(string $key): ?Response
    {
        $response = null;

        try {
            if ($this->cache instanceof CacheItemPoolInterface) { // PSR-6
                if ($this->cache->hasItem($key)) {
                    $response = $this->cache->getItem($key)->get();
                }
            } elseif ($this->cache instanceof CacheInterface) { // PSR-16
                $response = $this->cache->get($key);
            }
        } catch (\Psr\Cache\CacheException | \Psr\SimpleCache\CacheException $exception) {
            throw new CacheException($exception->getMessage());
        }

        if (!is_null($response) && !($response instanceof Response)) { // WTF
            throw new CacheException('Invalid cached response');
        }

        return $response;
    }

    /**
     * API base URL.
     *
     * @return string
     */
    protected function apiBaseUrl(): string
    {
        return static::API_URL . '/v' . $this->getVersion();
    }

    /**
     * Bulk API base URL.
     *
     * @return string
     */
    protected function bulkApiBaseUrl(): string
    {
        return static::BULK_API_URL . '/v' . $this->getVersion();
    }
}