# ZeroBounce API client

Non official PHP client for [ZeroBounce API](https://www.zerobounce.net/docs/email-validation-api-quickstart/).

## Installation

```
composer require nullform/zerobounce-client
```

## Usage

### Basic

```php
use Nullform\ZeroBounce;

$client = new ZeroBounce\Client($api_key);

try {

    $result = $client->validate($email);

} catch (ZeroBounce\Exceptions\AbstractException $exception) {

    echo $exception->getMessage();

}
```

### Caching

You can cache API responses if you use PSR-6 or PSR-16 caching in your project.
Just pass to `Client::caching()` your cache repository instance, TTL and prefix.

If the cache instance is passed, each successful response will be stored to the cache for the `$ttl` seconds.
With repeated requests with the same parameters, the response will be taken from the cache.

```php
// Set PSR-6 or PSR-16 cache instance, TTL (60) and cache keys prefix (zerobounce_)
$client->caching($cache, 60, 'zerobounce_');
```

### Logging

You can log your API calls by passing your own function to `Client::logFunction()`. Passed function will be called on every API call.
The function takes an instance of `\Nullform\ZeroBounce\Client` as a parameter.
For example:

```php
$log_func = function (ZeroBounce\Client $client) {
    file_put_contents('zerobounce-client.log', print_r($client->lastResponse(), true));
};

$client = new ZeroBounce\Client($api_key);
$client->logFunction($log_func);

$usage = $client->getUsage('2019-01-01');
```

Or you can just override the `Client::log()` method that is called on every TGStat API call.

## Methods

### Client::timeout()

`Client::timeout( [int $timeout = null ] ) : int`

Timeout (maximum time the request is allowed to take).
Get or set value.

### Client::caching()

`Client::caching( $cache, int $ttl [, string $prefix = '' ] ) : bool`

Caching of API responses.

You can pass `null` to disable caching (default).

### Client::logFunction()

`Client::logFunction( ?callable $func ) : bool`

The function for logging that will be called after every ZeroBounce API call.

The function takes a client instance as a parameter.

### Client::lastRequest()

`Client::lastRequest( void ) : Http\Request`

Get last request.

### Client::lastResponse()

`Client::lastResponse( void ) : Http\Response`

Get last response.

### Client::getCredits()

`Client::getCredits( void ) : int`

Get credit balance.
If a -1 is returned, that means your API Key is invalid.

[Validation API: Credit Balance](https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-credit-balance/)

### Client::validate()

`Client::validate( string $email [, ?string $ip_address = null ] ) : Models\Email`

Validate email.

[Validation API: Validate Emails](https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-validate-emails/)

### Client::getUsage()

`Client::getUsage( mixed $start_date [, mixed $end_date = 'now' ] ) : Models\Usage`

Get API usage.

`$start_date` and `$end_date` - timestamp or string that the strtotime(), DateTime and date_create() parser understands.

[Validation API: API Usage](https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-get-api-usage/)

### Client::bulkSendFile()

`Client::bulkSendFile( string $filename, string $type, Params\BulkSendFileParams $params ) : Models\BulkFile`

Send a csv or txt file for bulk email validation/scoring.

```php
$client = new Client($api_key);
$client->timeout(60);
$params = new Params\BulkSendFileParams();
$params->email_address_column = 2;
$file = $client->bulkSendFile($filename, Models\BulkFile::TYPE_VALIDATION, $params);
```

[Validation API: Send File](https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-send-file/)
|
[AI Scoring API: Send File](https://www.zerobounce.net/docs/ai-scoring-api/send-file/)

### Client::bulkFileStatus()

`Client::bulkFileStatus( string $file_id, string $type ) : Models\BulkFile`

Uploaded file status.

[Validation API: File Status](https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-file-status/)
|
[AI Scoring API: File Status](https://www.zerobounce.net/docs/ai-scoring-api/file-status/)

### Client::bulkGetFile()

`Client::bulkGetFile( string $file_id, string $type [, ?string $output_filename = null ] ) : string`

Get the validation/scoring results csv (or zip) file for the file been submitted using sendfile API.

[Validation API: Get File](https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-get-file/)
|
[AI Scoring API: Get File](https://www.zerobounce.net/docs/ai-scoring-api/get-file/)

### Client::bulkDeleteFile()

`Client::bulkDeleteFile( string $file_id, string $type ) : bool`

Delete the file that was submitted using sendfile API.
File can be deleted only when its status is Complete.

[Validation API: Delete File](https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-delete-file/)
|
[AI Scoring API: Delete File](https://www.zerobounce.net/docs/ai-scoring-api/delete-file/)

## Facade

You can use facade for quick email validation and balance check.

```php
use Nullform\ZeroBounce\Facade as ZeroBounce;

try {
    $result = ZeroBounce::validate($api_key, $email);
    $credits = ZeroBounce::getCredits($api_key);
} catch (\Exception $exception) {
    $error = $exception->getMessage();
}
```
