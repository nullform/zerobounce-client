<?php

use Nullform\ZeroBounce;
use Nullform\ZeroBounce\Models\BulkFile;

require '_autoload.php';

$api_key = '';
$filename = '';

$client = new ZeroBounce\Client($api_key);
$client->timeout(60);

try {

    $params = new ZeroBounce\Params\BulkSendFileParams();
    $params->email_address_column = 1;

    $file = $client->bulkSendFile($filename, BulkFile::TYPE_VALIDATION, $params);

    if ($file->file_id) {

        $file_status = $client->bulkFileStatus($file->file_id, BulkFile::TYPE_VALIDATION);

        $output = json_encode(
            $file_status,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );

        echo php_sapi_name() != 'cli' ? "<pre>$output</pre>" : $output;

    }

} catch (ZeroBounce\Exceptions\AbstractException $exception) {

    echo $exception->getMessage();

}