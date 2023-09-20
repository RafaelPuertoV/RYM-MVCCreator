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
    $methodList = array();
    {{CONTROLLER.METHODLIST}}
    if(isset($_GET['method'])){
        if(isset($_GET['action']) && isset($_GET['actionType'])&& $_GET['actionType']=='viewForm'){
            {{CONTROLLER.GET.BLOCK}}
        }  
    }
    $tbody='';
    $idx= 1;
    foreach ($methodList as $method) {
        $tbody.='<tr><td>'.($idx++).'</td><td>'.$method.'</td><td><a href="./?method='.$method.'&action=index&actionType=viewForm" target="_blank"> View </a></td></tr>';
    }
    $parameters = array(
        '{{HOMEPAGE.TBODY}}'=>$tbody
    );


    $containerView = \{{NAMESPACE}}\MVC\Http\HTTPResponse::renderView(\{{NAMESPACE}}\MVC\Controllers\MVCController::viewsPath()."homepage.php",$parameters) ;

    $parameters = array( 
        '{{DATABASE.NAME}}' => DB_NAME , 
        '{{MVC.NAMESPACE}}' => '{{NAMESPACE}} :: HOME PAGE',
        '{{APP.CONTAINER}}' => $containerView
    );
    $responseView = file_get_contents(\{{NAMESPACE}}\MVC\Controllers\MVCController::viewsPath().'Core/base.template.html');
    \{{NAMESPACE}}\MVC\Http\HTTPResponse::View($responseView,$parameters) ;
    exit();
}

?>