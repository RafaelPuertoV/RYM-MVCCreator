<?php

require ('PDO.inc.php');
require('RYMTable.php');
require('RYMColumn.php');

class RYMGenerator 
{
    var $db = null ;
    var $config;
    var $tableList;

    var $mvcPath;
    var $mvcModelBasePath;
    var $mvcModelPath;
    var $mvcControllerBasePath;

    var $modelBaseClassTmp ;
    var $modelClassTmp ;
    var $modelAttribItemTmp ;
    var $modelAttribGetSetTmp ;
      
    var $controllerClassTmp ;
    var $controllerAttribSetValueTmp ;

    var $mvcViewsPath;
    var $viewIndexClassTmp;

    var $publicPath;
      
    public function __construct() {
        $this->db = new RYMDatabase();
        $this->tableList = array();
        $this->config=include(__DIR__.'/../config.inc.php');
        $this->config["DataBaseClass"] = "MVCDataBase";
        $this->mvcPath = $this->config['MVC_PATH'].$this->config['MVC_NAMESPACE'].'/';

        $this->loadTablesInfo();

        if(!file_exists($this->mvcPath)){
            mkdir($this->mvcPath,777,true);
        }
        $this->mvcModelPath = $this->mvcPath.'src/Models/';
        $this->mvcModelBasePath = $this->mvcModelPath.'Base/';
       
        if(!file_exists($this->mvcModelBasePath)){
            mkdir($this->mvcModelBasePath,777,true);
        }

        $this->mvcControllerBasePath = $this->mvcPath.'src/Controllers/';
       
        if(!file_exists($this->mvcControllerBasePath)){
            mkdir($this->mvcControllerBasePath,777,true);
        }

        $this->mvcViewsPath = $this->mvcPath.'src/Views/';
       
        if(!file_exists($this->mvcViewsPath)){
            mkdir($this->mvcViewsPath,777,true);
        }

        $this->publicPath = $this->mvcPath.'public/';
       
        if(!file_exists($this->publicPath)){
            mkdir($this->publicPath,777,true);
        }

        # Loading Table Info;

        # Loading Model Templates
        $this->modelBaseClassTmp = file_get_contents(__DIR__.'/templates/model/model.baseclass.template.tpl');
        $this->modelClassTmp = file_get_contents(__DIR__.'/templates/model/model.class.template.tpl');
        $this->modelAttribItemTmp = file_get_contents(__DIR__.'/templates/model/model.attributes.item.tpl');
        $this->modelAttribGetSetTmp = file_get_contents(__DIR__.'/templates/model/model.attributes.getset.template.tpl');

        # Loading Controller Templates
        $this->controllerClassTmp = file_get_contents(__DIR__.'/templates/controller/controller.class.template.tpl');
        $this->controllerAttribSetValueTmp = file_get_contents(__DIR__.'/templates/controller/controller.item.setvalues.tpl');
       
        
        # Loading Controller Templates
        $this->viewIndexClassTmp = file_get_contents(__DIR__.'/templates/views/index.template.tpl');
       
    }

    function loadTablesInfo(){
        $tableList = $this->db->getTables();
        foreach ($tableList as $table) {
            $cTable = new RYMTable();
            $cTable->tableName=$table;
            $columns = $this->db->getColumns($table);
            foreach ($columns as $column) {
                $cColumn= new RYMColumn();
                #COLUMN_NAME, DATA_TYPE,CHARACTER_MAXIMUM_LENGTH
                $cColumn->columnName=$column['COLUMN_NAME'];
                $cColumn->DBType=$column['DATA_TYPE'];
                $cColumn->DBTypeLength=$column['CHARACTER_MAXIMUM_LENGTH'];
                $column['COLUMN_KEY']=='PRI';
                if($column['COLUMN_KEY']=='PRI'){
                    $cTable->primaryKeys[]=$cColumn->columnName;
                }
                $cTable->columns[]=$cColumn;
            }
            $cTable->foreingKeys = $this->db->getForeingeys($table);
            if(count($cTable->foreingKeys)>0){
                array_unshift($this->tableList, $cTable);
            }else{
                $this->tableList[]=$cTable;
            }
        }
    }


    function generateCompose() {
        $tmpList = array(
            '{{NAMESPACE}}' => $this->config['MVC_NAMESPACE'],
        );
        $dbClassTmp = file_get_contents(__DIR__.'/templates/composer.template.tpl');

        $classTmp = strtr( $dbClassTmp , $tmpList); 
        
        # Creating file:
        $modelFileName = "composer.json";
        $myfile = fopen($this->mvcPath.$modelFileName, "w");
        fwrite($myfile, $classTmp );

        return $modelFileName.' <span style="background:green; color:white; border-radius: 30px;"> &nbsp;&nbsp;created&nbsp;&nbsp; </span>';
    }

    function generateMVCFiles() {
        $this->copyFolder(__DIR__.'/templates/MVC/', $this->mvcPath.'src/MVC');
        return 'MVCFiles <span style="background:green; color:white; border-radius: 30px;"> &nbsp;&nbsp;created&nbsp;&nbsp; </span>';
    }

    function generateConfigFile() {

        $cfgTemplate = file_get_contents(__DIR__.'/templates/config.template.tpl');
        $tmpList = array(
            '{{MySQL.SERVER}}' => $this->config['hostDB'],
            '{{DB.USER}}' => $this->config['usernameDB'],
            '{{DB.PASSWORD}}' => $this->config['passwordDB'],
            '{{DB.NAME}}' => $this->config['nameDB'],
        );
        $classTmp = strtr( $cfgTemplate , $tmpList); 
        # Creating file:
        $modelFileName = "config.php";
        $myfile = fopen($this->mvcPath.$modelFileName, "w");
        fwrite($myfile, $classTmp );
        fclose($myfile);

        return $modelFileName.' <span style="background:green; color:white; border-radius: 30px;"> &nbsp;&nbsp;created&nbsp;&nbsp; </span>';
    }

    function createMVCBase(){
        $baseHTML = '<div style="background: #dfdfdf;"> <span> MVC Base classes: </span> <br> <ul>';
        $baseHTML .= "<li> ".$this->generateConfigFile()."</li>";
        $baseHTML .= "<li> ".$this->generateMVCFiles()."</li>";
        $baseHTML .= "<li> ".$this->generateCompose()."</li>";
        $baseHTML .= "<li> ".$this->generatePublicIndex()."</li>";
        $baseHTML .= "<li> ".$this->generatePublicAPI()."</li>";
        $baseHTML.='</div>';
        return $baseHTML;
    }

    function generateModel(RYMTable $_table) {
      
        #echo RYMDatabase::getArrayToHTMLTable($attributes); die();
        $attribList ='';
        $attribGetSetList ='';
        
        foreach ( $_table->columns as $attrib) {
            $attribList .= str_replace('{{ATTRIBUTES.NAME}}',$attrib->columnName, $this->modelAttribItemTmp ); 
            $attribGetSetList .= str_replace('{{ATTRIBUTES.NAME}}',$attrib->columnName, $this->modelAttribGetSetTmp ); 
        }

        $tmpList = array(
            '{{NAMESPACE}}' => $this->config['MVC_NAMESPACE'],
            '{{CLASS.NAME}}'=> $this->config['MVC_PREFIX'].$_table->getModelNameClass(),
            '{{TABLE.NAME.LOWCASE}}'=> $_table->tableName,
            '{{TABLE.PRIMARYKEYS.ARRAY}}'=> $_table->getPrimaryKeysTemplate(),
            '{{ATTRIBUTES.LIST}}'=> $attribList ,
            '{{ATTRIBUTES.GET.SET}}'=> $attribGetSetList,
        );

        $classTmp = strtr( $this->modelBaseClassTmp , $tmpList); 
        
        # Creating Base file:
        $modelFileName = $this->config['MVC_PREFIX'].$_table->getModelNameClass()."Base.php";
        #echo $this->mvcModelBasePath.$modelFileName;die();
        $mysBaseFile = fopen($this->mvcModelBasePath.$modelFileName, "w");
        fwrite($mysBaseFile, $classTmp );
        fclose($mysBaseFile);

        $tmpList = array(
            '{{NAMESPACE}}' => $this->config['MVC_NAMESPACE'],
            '{{CLASS.NAME}}'=> $this->config['MVC_PREFIX'].$_table->getModelNameClass(),
            '{{TABLE.NAME.LOWCASE}}'=> $_table->tableName,
            '{{ATTRIBUTES.LIST}}'=> $attribList ,
            '{{ATTRIBUTES.GET.SET}}'=> $attribGetSetList,
        );

        $classTmp = strtr( $this->modelClassTmp , $tmpList); 
        # Creating Additional Class file:
        $modelFileName = $this->config['MVC_PREFIX'].$_table->getModelNameClass().".php";
        $myfile = fopen($this->mvcModelPath.$modelFileName, "w");
        fwrite($myfile, $classTmp );
        fclose($myfile);

        return $modelFileName.' <span style="background:green; color:white; border-radius: 30px;"> &nbsp;&nbsp;created&nbsp;&nbsp; </span>';
    }

    function generateModelAll() {
      

        $modelHtml = '<div style="background: #dfdfdf;"> <h2><span> Models: </span> </h2> <br> <ul>';
        foreach ($this->tableList as $table) {
            $modelHtml .= "<li> ".$this->generateModel($table)."</li>";
        }
        $modelHtml.='</div>';
        return $modelHtml;
    }

    function generateControllerAll() {
      
        $modelHtml = '<div style="background: #dfdfdf;"> <h2><span> Controllers: </span> </h2><br> <ul>';
        foreach ($this->tableList as $table) {
            if(count($table->foreingKeys)==0 ){
                $modelHtml .= "<li> ".$this->generateController($table)."</li>";
            }
        }
        $modelHtml.='</div>';
        return $modelHtml;
    }


    function generateViewsAll() {
        $this->generateViewsCore();
        $viewsHtml = '<div style="background: #dfdfdf;"> <h2><span> Views: </span> </h2><br> <ul>';
        foreach ($this->tableList as $table) {
            if(count($table->foreingKeys)==0 ){
                $viewsHtml .= "<li> ".$this->generateViews($table)."</li>";
            }
        }
        $viewsHtml.='</div>';
        return $viewsHtml;
    }

    ###########################################################
    #                                                         # 
    #                       CONTROLLERS                       #
    #                                                         #
    ###########################################################

    function generateController(RYMTable $_table) {
      
        #echo RYMDatabase::getArrayToHTMLTable($attributes); die();
        $attribList ='';
        $attribSetValues ='';
        
        foreach ( $_table->columns as $attrib) {
            $attribSetValues .= str_replace('{{ATTRIBUTES.NAME}}',$attrib->columnName, $this->controllerAttribSetValueTmp ); 
        }

        $tmpList = array(
            '{{NAMESPACE}}' => $this->config['MVC_NAMESPACE'],
            '{{CLASS.NAME}}'=> $this->config['MVC_PREFIX'].$_table->getModelNameClass(),
            '{{CLASS.PRIMARYKEY}}'=> count($_table->primaryKeys)?$_table->primaryKeys[0]:'id',
            '{{CONTROLLER.SETITEM.VALUES}}'=> $attribSetValues 
        );

        $classTmp = strtr( $this->controllerClassTmp , $tmpList); 
        
        # Creating Base file:
        $controllerFileName = $this->config['MVC_PREFIX'].$_table->getModelNameClass()."Controller.php";
        #echo $this->mvcControllerBasePath.$controllerFileName;die();
        $mysBaseFile = fopen($this->mvcControllerBasePath.$controllerFileName, "w");
        fwrite($mysBaseFile, $classTmp );
        fclose($mysBaseFile);

        return $controllerFileName.' <span style="background:green; color:white; border-radius: 30px;"> &nbsp;&nbsp;created&nbsp;&nbsp; </span>';
    }


    ###########################################################
    #                                                         # 
    #                       VIEWS                             #
    #                                                         #
    ###########################################################

    function copyFolder($_currentFolder, $_destPath){
        if ($_currentFolder == '') {
            $_currentFolder = realpath('./public/css/');
        }
        if(!file_exists($_destPath)){
            mkdir($_destPath,777,true);
        }
        $List  = scandir($_currentFolder);
        $isEmpty = true;
        foreach ($List as $pth) {
            if ($pth != '.' && $pth != '..') {
                $isEmpty = false;
                break;
            }
        }

        foreach ($List as $pth) {
            if ($pth == '.' || $pth == '..') {
                continue;
            }
            if (is_dir($_currentFolder . '/' . $pth)) {
                self::copyFolder($_currentFolder . '/' . $pth, $_destPath . '/' . $pth) ;
            } elseif (is_file($_currentFolder . '/' . $pth)) {
                $tmpList = array(
                    '{{NAMESPACE}}' => $this->config['MVC_NAMESPACE'],
                );

                $fileInfo = file_get_contents($_currentFolder . '/' . $pth);
                $view = strtr( $fileInfo , $tmpList);
                $mysBaseFile = fopen($_destPath . '/' . $pth, "w");
                fwrite($mysBaseFile, $view );
                fclose($mysBaseFile);
            }
        }
    }
    function generatePublicIndex() {
        $attribSetValues ='';

        $this->copyFolder(__DIR__.'/templates/public/img/', $this->publicPath.'/img/');
        $this->copyFolder(__DIR__.'/templates/public/css/', $this->publicPath.'/css/');
        $this->copyFolder(__DIR__.'/templates/public/js/', $this->publicPath.'/js/');

        ### HOMEPAGE ###

        $viewHowe = file_get_contents(__DIR__.'/templates/views/homepage.php');
        
        # Creating Base file:
        $viewHoweFileName = "homepage.php";
        #echo $this->mvcControllerBasePath.$viewHoweFileName;die();
        $mysBaseFile = fopen($this->mvcViewsPath.$viewHoweFileName, "w");
        fwrite($mysBaseFile, $viewHowe );
        fclose($mysBaseFile);

        $controllerRequestTemp = file_get_contents(__DIR__.'/templates/public/reques.get.tpl');
        $controllerRequestApi = '';
        $controllerMethodList = '';
        foreach ( $this->tableList as $table) {
            if(count($table->foreingKeys)>0 ){
                continue;
            }
            $controllerMethodList .= '
    $methodList[] = "'.$this->config['MVC_PREFIX'].$table->getModelNameClass().'";'; 
            $tmpList = array(
                '{{NAMESPACE}}' => $this->config['MVC_NAMESPACE'],
                '{{CLASS.NAME}}'=> $this->config['MVC_PREFIX'].$table->getModelNameClass(),
                '{{CLASS.PRIMARYKEY}}'=> count($table->primaryKeys)?$table->primaryKeys[0]:'id',
            );
            
            $controllerRequestApi .= strtr( $controllerRequestTemp , $tmpList);
        }

        $viewIndex = file_get_contents(__DIR__.'/templates/public/index.php');
        $tmpList = array(
            '{{NAMESPACE}}' => $this->config['MVC_NAMESPACE'],
            '{{CLASS.NAME}}'=> $this->config['MVC_PREFIX'].$table->getModelNameClass()."Controller",
            '{{CLASS.PRIMARYKEY}}'=> count($table->primaryKeys)?$table->primaryKeys[0]:'id',
            '{{CONTROLLER.METHODLIST}}'=> $controllerMethodList,
            '{{CONTROLLER.GET.BLOCK}}'=> $controllerRequestApi
        );
        $view = strtr( $viewIndex , $tmpList);
        # Creating Base file:
        $viewFileName = "index.php";
        #echo $this->mvcControllerBasePath.$viewFileName;die();
        $mysBaseFile = fopen($this->publicPath.$viewFileName, "w");
        fwrite($mysBaseFile, $view );
        fclose($mysBaseFile);

        return $viewFileName.' <span style="background:green; color:white; border-radius: 30px;"> &nbsp;&nbsp;created&nbsp;&nbsp; </span>';
    }

    function generatePublicAPI() {


      
        $controllerRequestTemp = file_get_contents(__DIR__.'/templates/public/api.reques.get.tpl');
        $controllerRequestApi = '';
        foreach ( $this->tableList as $table) {
            if(count($table->foreingKeys)>0 ){
                continue;
            }
            $tmpList = array(
                '{{NAMESPACE}}' => $this->config['MVC_NAMESPACE'],
                '{{CLASS.NAME}}'=> $this->config['MVC_PREFIX'].$table->getModelNameClass(),
                '{{CLASS.PRIMARYKEY}}'=> count($table->primaryKeys)?$table->primaryKeys[0]:'id',
            );
            $controllerRequestApi .= strtr( $controllerRequestTemp , $tmpList);
        }

        $viewIndex = file_get_contents(__DIR__.'/templates/public/api.php');
        $tmpList = array(
            '{{NAMESPACE}}' => $this->config['MVC_NAMESPACE'],
            '{{CLASS.NAME}}'=> $this->config['MVC_PREFIX'].$table->getModelNameClass()."Controller",
            '{{CLASS.PRIMARYKEY}}'=> count($table->primaryKeys)?$table->primaryKeys[0]:'id',
            '{{CONTROLLER.GET.BLOCK}}'=> $controllerRequestApi
        );
        $view = strtr( $viewIndex , $tmpList);
        # Creating Base file:
        $viewFileName = "api.php";
        #echo $this->mvcControllerBasePath.$viewFileName;die();
        $mysBaseFile = fopen($this->publicPath.$viewFileName, "w");
        fwrite($mysBaseFile, $view );
        fclose($mysBaseFile);

        return $viewFileName.' <span style="background:green; color:white; border-radius: 30px;"> &nbsp;&nbsp;created&nbsp;&nbsp; </span>';
    }

    
    function generateViewsCore() {
        $this->copyFolder(__DIR__.'/templates/views/core/', $this->mvcViewsPath.'/Core/');
    }

    function generateViews(RYMTable $_table) {
      
        $attribSetValues ='';
        if(!file_exists($this->mvcViewsPath.'/'.$this->config['MVC_PREFIX'].$_table->getModelNameClass())){
            mkdir($this->mvcViewsPath.'/'.$this->config['MVC_PREFIX'].$_table->getModelNameClass(),777,true);
        }

        $tmpList = array(
            '{{NAMESPACE}}' => $this->config['MVC_NAMESPACE'],
            '{{CLASS.NAME}}'=> $this->config['MVC_PREFIX'].$_table->getModelNameClass(),
            '{{CLASS.PRIMARYKEY}}'=> $_table->primaryKeys[0],
            '{{CONTROLLER.SETITEM.VALUES}}'=> $attribSetValues 
        );

        $classTmp = strtr( $this->viewIndexClassTmp , $tmpList); 
        
        # Creating Index file:
        $ViewsFileName = "index.php";
        #echo $this->mvcViewsBasePath.$ViewsFileName;die();
        $mysBaseFile = fopen($this->mvcViewsPath.'/'.$this->config['MVC_PREFIX'].$_table->getModelNameClass()."/index.php", "w");
        fwrite($mysBaseFile, $classTmp );
        fclose($mysBaseFile);

        return $_table->getModelNameClass().'/index.php  <span style="background:green; color:white; border-radius: 30px;"> &nbsp;&nbsp;created&nbsp;&nbsp; </span>';
    }
}
?>