<?php

class RYMConfig {
    private $config = array();

    private function __construct(){
        $this->config = include(__DIR__.'/../config.inc.php');
    }

    public static function getInstance(){
        static $instance = null;
        if($instance === null){
            $instance = new RYMConfig();
        }
        return $instance;
    }

    public function getMVCPath(){
        return $this->config['MVC_PATH'].$this->config['MVC_NAMESPACE'].'/';
    }

    public function getMVCNamespace(){
        return $this->config['MVC_NAMESPACE'];
    }
    public function getMVCPrefix(){
        return $this->config['MVC_PREFIX'];
    }

    public function getMVCModelPath(){
        $path = $this->getMVCPath().'src/Models/';
        if(!file_exists($path)){
            umask(000);
            mkdir($path,775,true);
        }
        return $path;
    }

    public function getMVCControllerPath(){
        $path = $this->getMVCPath().'src/Controllers/';
        if(!file_exists($path)){
            umask(000);
            mkdir($path,775,true);
        }
        return $path;
    }

    public function getMVCViewsPath(){
        $path = $this->getMVCPath().'src/Views/';
        if(!file_exists($path)){
            umask(000);
            mkdir($path,775,true);
        }
        return $path;
    }

    public function getMVCBasePath(){
        $path = $this->getMVCModelPath().'Base/';
        if(!file_exists($path)){
            umask(000);
            mkdir($path,775,true);
        }
        return $path;
    }

    public function getHostDB(){

        return $this->config['hostDB'];
    }
    public function getUsernameDB(){

        return $this->config['usernameDB'];
    }
    public function getPasswordDB(){

        return $this->config['passwordDB'];
    }
    public function getNameDB(){

        return $this->config['nameDB'];
    }

    public function getAuthor(){
        return "\n/**
            \n * Base Model 
            \n *
            \n * @category   Pro
            \n * @package    RYM-MVCCreator
            \n * @author     Rafael Puerto V.<rafael.puerto.v@gmail.com>
            \n * @link       https://github.com/RafaelPuertoV/RYMMVCCreator
            \n * @see        RYM-MVCreator
            \n */
            \n";
    }
}