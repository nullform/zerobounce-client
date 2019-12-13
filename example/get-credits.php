<?php

use Nullform\ZeroBounce;

require '_autoload.php';

$api_key = '';

$client = new ZeroBounce\Client($api_key);

try {

    $credits = $client->getCredits();

    echo $credits;

} catch (ZeroBounce\Exceptions\AbstractException $exception) {

    echo $exception->getMessage();

}