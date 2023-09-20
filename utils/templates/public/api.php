<?php
include_once(__DIR__ . '/../config.php');
if(!file_exists('../vendor/autoload.php')){
    echo 'Please execute <b>composer install</b>.';
}
require '../vendor/autoload.php';

if(isset($_GET)){
    if(isset($_POST)){
        $tmp = array_merge($_GET,$_POST);
        $_GET = $tmp;
    }
    if(isset($_GET['method'])){
        if(isset($_GET['action']) && isset($_GET['actionType'])&& $_GET['actionType']=='API1.1'){
            {{CONTROLLER.GET.BLOCK}}
        }  
        
        echo  json_encode(array(
            'status' => 404
        ));
        exit();
    }
}

?>