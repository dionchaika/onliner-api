# Onliner API
The Unofficial www.onliner.by API

## Requirements
1. PHP 7.1.3 or higher

## Basic usage
```php
<?php

require_once 'vendor/autoload.php';

use API\Onliner\Onliner;
use API\Onliner\Adverts\Flat;

set_time_limit(0);
header('Content-Type: text/plain');

/////////// CONFIG ///////////
$debug     = true;
$debugFile = null;
$user      = 'user_name';
$password  = 'user_password';
//////////////////////////////

$onliner = new Onliner($debug, $debugFile);

try {

    $onliner->login($user, $password);

} catch (Throwable $e) {

    echo 'Something wrong is going on: '.$e->getMessage();
    exit(-1);

}

///////////////////////// IMAGES UPLOAD /////////////////////////
$images[] = $onliner->uploadImage('images/image1.jpg')['images'];
$images[] = $onliner->uploadImage('images/image2.jpg')['images'];
$images[] = $onliner->uploadImage('images/image3.jpg')['images'];
$images[] = $onliner->uploadImage('images/image4.jpg')['images'];
$images[] = $onliner->uploadImage('images/image5.jpg')['images'];
/////////////////////////////////////////////////////////////////

/////////////////////////// ADVERT CREATION ///////////////////////////
$flatAdvert = new Flat(
    $images,
    true,
    Flat::findWallingTypeByName('панельный'),
    true,
    3,
    true,
    Flat::findParkingTypeByName('на улице'),
    5,
    9,
    false,
    50000,
    'USD',
    'Мое агентство',
    'УНП моего агентства',
    'Агент',
    50.2,
    40,
    10.2,
    2.0,
    1978,
    'Описание квартиры',
    ['+375295555555', '+375335555555', '+375445555555'],
    9,
    18
);
///////////////////////////////////////////////////////////////////////

try {

    $flatAdvert->setAddressInfo($onliner->getAddressInfo('Брест Мицкевича ул.'));

    $result = $onliner->postAdvert($flatAdvert);

    echo $result['id'];
    echo $result['url'];

} catch (Throwable $e) {

    echo 'Something wrong is going on: '.$e->getMessage();
    exit(-1);

}
```
