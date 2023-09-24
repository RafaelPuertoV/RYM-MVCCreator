<?php
include_once(__DIR__ . '/../config.php');
if(!file_exists('../vendor/autoload.php')){
    echo 'Please execute <b>composer install</b>.';
}
require '../vendor/autoload.php';
use \{{NAMESPACE}}\MVC\MVCRequesHandler;

$rHandler = new MVCRequesHandler();
$rHandler->handle();
