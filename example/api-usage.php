<?php

use Nullform\ZeroBounce;

require '_autoload.php';

$api_key = '';

$client = new ZeroBounce\Client($api_key);

try {

    $result = $client->getUsage('2019-01-01');

    $output = json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    echo php_sapi_name() != 'cli' ? "<pre>$output</pre>" : $output;

} catch (ZeroBounce\Exceptions\AbstractException $exception) {

    echo $exception->getMessage();

}