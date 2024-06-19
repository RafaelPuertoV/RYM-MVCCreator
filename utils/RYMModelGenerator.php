<?php


require_once ('PDO.inc.php');
require_once('RYMTable.php');
require_once('RYMColumn.php');

class RYMModelGenerator{
    private $rymtTable;
    private $config;
    private $modelBaseClassTmp;
    private $modelClassTmp;
    private $modelAttribItemTmp;
    private $modelAttribGetSetTmp;

    function __construct(RYMTable $table){

        $this->rymtTable = $table;
        $this->config=RYMConfig::getInstance();
          # Loading Model Templates
        $this->modelBaseClassTmp = file_get_contents(__DIR__.'/templates/model/model.baseclass.template.tpl');
        $this->modelClassTmp = file_get_contents(__DIR__.'/templates/model/model.class.template.tpl');
        $this->modelAttribItemTmp = file_get_contents(__DIR__.'/templates/model/model.attributes.item.tpl');
        $this->modelAttribGetSetTmp = file_get_contents(__DIR__.'/templates/model/model.attributes.getset.template.tpl');
  
    }

    public function generateModel(){
        $modelBase = $this->generateModelBase();

        $model = $this->generateModelRepository();
        return $modelBase && $model;
    }

    public function generateModelBase(){
        $attribList ='';
        $attribGetSetList ='';
        
        foreach ( $this->rymtTable->columns as $attrib) {
            //$this->dbColumnTypes[$attrib->DBType]=$attrib->DBType;
            $attribProperties = "\n/**\n    * DBType: ".$attrib->DBType ;
            if($attrib->DBTypeLength!='')
                $attribProperties .= "\n    * DBTypeLength: ".$attrib->DBTypeLength ;
            if($attrib->ENUMOptions!='')
                $attribProperties .= "\n    * ENUMOptions: ".$attrib->ENUMOptions ;
            $attribProperties .= "\n*/";
            $tmpList = array(
                '{{ATTRIBUTES.NAME}}' => $attrib->columnName,
                '{{ATTRIBUTES.TYPE}}' => $attrib->getPHPType(),
                '{{ATTRIBUTES.PROPERTIES}}' => $attribProperties,
                '{{ATTRIBUTES.DEFAULT.VALUE}}'=> $attrib->getPHPDefault()
            );
            $attribList .=  strtr( $this->modelAttribItemTmp , $tmpList); 
            $attribGetSetList .=  strtr( $this->modelAttribGetSetTmp , $tmpList); 
        }

        $tmpList = array(
            '{{NAMESPACE}}' => $this->config->getMVCNamespace(),
            '{{CLASS.PREFIX}}' => $this->config->getMVCPrefix(),
            '{{CLASS.NAME}}'=> $this->getModelNameClass(),
            '{{TABLE.NAME.LOWCASE}}'=> $this->rymtTable->tableName,
            '{{TABLE.PRIMARYKEYS.ARRAY}}'=> $this->rymtTable->getPrimaryKeysTemplate(),
            '{{ATTRIBUTES.LIST}}'=> $attribList ,
            '{{ATTRIBUTES.GET.SET}}'=> $attribGetSetList,
        );

        $classTmp = strtr( $this->modelBaseClassTmp , $tmpList); 
        
        # Creating Base file:
        $modelFileName = $this->getModelBaseFile();

        $mysBaseFile = fopen($this->config->getMVCBasePath().$modelFileName, "w");
        fwrite($mysBaseFile, $classTmp );
        fclose($mysBaseFile);

        
        return true;
    }

    public function generateModelRepository(){
        $tmpList = array(
            '{{NAMESPACE}}' => $this->config->getMVCNamespace(),
            '{{CLASS.PREFIX}}' => $this->config->getMVCPrefix(),
            '{{CLASS.NAME}}'=> $this->getModelNameClass(),
            '{{TABLE.NAME.LOWCASE}}'=> $this->rymtTable->tableName,
        );

        $classTmp = strtr( $this->modelClassTmp , $tmpList); 
        # Creating Additional Class file:
        $modelFileName = $this->getModelFile();
        $myfile = fopen($this->config->getMVCModelPath().$modelFileName, "w");
        fwrite($myfile, $classTmp );
        fclose($myfile);

        return true;
    }

    function getModelNameClass(){
        return str_replace(' ', '', ucwords( str_replace('_',' ', $this->rymtTable->tableName ) ) );
    }

    function getModelFile(){
        return $this->config->getMVCPrefix().$this->getModelNameClass().".php";
    }

    function getModelBaseFile(){
        return  $this->config->getMVCPrefix().$this->getModelNameClass()."Base.php";
    }
}