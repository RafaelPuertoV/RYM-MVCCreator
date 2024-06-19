<?php


require_once('PDO.inc.php');
require_once('RYMTable.php');
require_once('RYMColumn.php');

class RYMViewsGenerator{
    private $rymtTable;
    private $config;
    private $viewIndexClassTmp;

    function __construct(RYMTable $table){
        $this->rymtTable = $table;
        $this->config=RYMConfig::getInstance();
        # Loading Controller Templates
        $this->viewIndexClassTmp = file_get_contents(__DIR__.'/templates/views/index.template.tpl');
    }

    public function generateView(){
        $attribSetValues ='';
        
        $tmpList = array(
            '{{NAMESPACE}}' => $this->config->getMVCNamespace(),
            '{{CLASS.PREFIX}}' => $this->config->getMVCPrefix(),
            '{{CLASS.NAME}}'=> $this->getViewNameClass(),
            '{{CLASS.PRIMARYKEY}}'=> $this->rymtTable->primaryKeys[0],
            '{{CONTROLLER.SETITEM.VALUES}}'=> $attribSetValues 
        );

        $classTmp = strtr( $this->viewIndexClassTmp , $tmpList); 
        
        $mysBaseFile = fopen($this->config->getMVCViewsPath().$this->getViewFile(), "w");
        fwrite($mysBaseFile, $classTmp );
        fclose($mysBaseFile);

        return true;
    }

    function getViewNameClass(){
        return str_replace(' ', '', ucwords( str_replace('_',' ', $this->rymtTable->tableName ) ) );
    }

    function getViewFile(){
        $path = $this->config->getMVCPrefix().$this->getViewNameClass();
        if(!file_exists($this->config->getMVCViewsPath().$path)){
            umask(000);
            mkdir($this->config->getMVCViewsPath().$path,775,true);
        }
        return $path."/index.php";
    }

}