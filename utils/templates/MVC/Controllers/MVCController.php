<?php

namespace {{NAMESPACE}}\MVC\Controllers;

class MVCController
{
    
    public static function basePath()
    {
        return __DIR__.'/../../../';
    }

    public static function publicPath()
    {
        return self::basePath().'public/'; 
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

    public static function getContollerClasses()
    {
        $List  = scandir(self::ControllersPath());
        $ctllrList = array();
        foreach ($List as $pth) {
            if ($pth == '.' || $pth == '..') {
                continue;
            }
            if (is_file(self::ControllersPath() . $pth)) {
                $ctllrList[] = str_replace(".php", "", $pth) ;
            } 
        }
        return $ctllrList;
    }
}