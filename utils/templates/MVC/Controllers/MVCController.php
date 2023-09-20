<?php

namespace {{NAMESPACE}}\MVC\Controllers;

class MVCController
{
    
    public static function basePath()
    {
        return __DIR__.'/../../../';
    }

    public static function srcPath()
    {
        return self::basePath().'src/';
    }


    public static function modelsPath()
    {
        return self::srcPath().'Model/';
    }


    public static function ControllersPath()
    {
        return self::srcPath().'Controllers/';
    }


    public static function viewsPath()
    {
        return self::srcPath().'Views/';
    }
}

