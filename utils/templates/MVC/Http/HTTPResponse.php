<?php

namespace {{NAMESPACE}}\MVC\Http;

class HTTPResponse
{
    public static function json($_object){
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($_object);
    }

    public static function renderView($_templateFile, $_valueList ){
        $teplateView =  $_templateFile;
        if( is_file($_templateFile) && file_exists($_templateFile)){
            $teplateView = file_get_contents($_templateFile);
        }
        $_valueList['{{WEB.ROOT}}']= \{{NAMESPACE}}\MVC\MVCRequesHandler::$web_root;
        return strtr( $teplateView , $_valueList);
    }

    public static function View($_templateFile, $_valueList ){
        header('Content-Type: text/html; charset=utf-8');
        echo  self::renderView($_templateFile, $_valueList );
    }
}
