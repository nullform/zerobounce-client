<?php

use Nullform\ZeroBounce;
use Nullform\ZeroBounce\Models\BulkFile;

require '_autoload.php';

$api_key = '';
$file_id = '';

$client = new ZeroBounce\Client($api_key);

try {

    $file = $client->bulkGetFile($file_id, BulkFile::TYPE_VALIDATION, 'result');

} catch (ZeroBounce\Exceptions\AbstractException $exception) {

    echo $exception->getMessage();

}