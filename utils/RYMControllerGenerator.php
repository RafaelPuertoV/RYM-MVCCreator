<?php


require_once('PDO.inc.php');
require_once('RYMTable.php');
require_once('RYMColumn.php');

class RYMControllerGenerator{
    private $rymtTable;
    private $config;
    private $controllerClassTmp;
    private $controllerAttribSetValueTmp;

    function __construct(RYMTable $table){
        $this->rymtTable = $table;
        $this->config=RYMConfig::getInstance();

        # Loading Controller Templates
        $this->controllerClassTmp = file_get_contents(__DIR__.'/templates/controller/controller.class.template.tpl');
        $this->controllerAttribSetValueTmp = file_get_contents(__DIR__.'/templates/controller/controller.item.setvalues.tpl');
         
    }

    public function generateController(){
        return $this->generateControllerClass() ;
    }

    public function generateControllerClass(){

         #echo RYMDatabase::getArrayToHTMLTable($attributes); die();
         $attribList ='';
         $attribSetValues ='';
         $primaryKeysArray ='';
         
         foreach ( $this->rymtTable->columns as $attrib) {
             $attribSetValues .= str_replace('{{ATTRIBUTES.NAME}}',$attrib->columnName, $this->controllerAttribSetValueTmp ); 
         }
 
         foreach ( $this->rymtTable->primaryKeys as $pKey) {
             $primaryKeysArray .=  '"'.$pKey.'" => $_request["'.$pKey.'"],'; 
         }
 
         $tmpList = array(
             '{{NAMESPACE}}' => $this->config->getMVCNamespace(),
             '{{CLASS.PREFIX}}' => $this->config->getMVCPrefix(),
             '{{CLASS.NAME}}'=> $this->getControllerNameClass(),
             '{{CLASS.PRIMARYKEY}}'=> count($this->rymtTable->primaryKeys)?$this->rymtTable->primaryKeys[0]:'id',
             '{{CONTROLLER.SETITEM.VALUES}}'=> $attribSetValues ,
             '{{PRIMARYKEYS.FINDBY}}' => $primaryKeysArray,
         );
 
         $classTmp = strtr( $this->controllerClassTmp , $tmpList); 
         
         # Creating Base file:
         $controllerFileName = $this->getControllerFile();
         #echo $this->mvcControllerBasePath.$controllerFileName;die();
         $mysBaseFile = fopen($this->config->getMVCControllerPath().$controllerFileName, "w");
         fwrite($mysBaseFile, $classTmp );
         fclose($mysBaseFile);
 
         return true;
    }

    public function getControllerNameClass(){
        return str_replace(' ', '', ucwords( str_replace('_',' ', $this->rymtTable->tableName ) ) );
    }

    function getControllerFile(){
        return  $this->config->getMVCPrefix().$this->getControllerNameClass()."Controller.php";
    }

}