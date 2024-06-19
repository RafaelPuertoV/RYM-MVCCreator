<?php

require ('PDO.inc.php');
require('RYMTable.php');
require('RYMColumn.php');
require ('RYMConfig.php');
require ('RYMModelGenerator.php');
require ('RYMControllerGenerator.php');
require ('RYMViewsGenerator.php');
class RYMGenerator 
{
    var $db = null ;

    var $dbColumnTypes;
    var $tableList;
    var $config;

    var $viewIndexClassTmp;

    var $publicPath;
      
    public function __construct() {
        $this->db = new RYMDatabase();
        $this->dbColumnTypes=array();
        $this->tableList = array();
        $this->config=RYMConfig::getInstance();

        $this->loadTablesInfo();
        $this->publicPath = $this->config->getMVCPath().'public/';
       
        if(!file_exists($this->publicPath)){
            umask(000);
            mkdir($this->publicPath,777,true);
        }

       
    }

    function loadTablesInfo(){
        $tableList = $this->db->getTables();
        foreach ($tableList as $table) {
            $cTable = new RYMTable($table);
            $columns = $this->db->getColumns($table);
            foreach ($columns as $column) {
                $cColumn= new RYMColumn();
                #COLUMN_NAME, DATA_TYPE,CHARACTER_MAXIMUM_LENGTH
                $cColumn->columnName=$column['COLUMN_NAME'];
                $cColumn->DBType=$column['DATA_TYPE'];
                $cColumn->DBTypeLength=$column['CHARACTER_MAXIMUM_LENGTH'];
                if($column['DATA_TYPE']=='enum'){
                    $cColumn->ENUMOptions=  str_replace('(','',str_replace(')','',substr($column['COLUMN_TYPE'],4,strlen($column['COLUMN_TYPE'])-4)));
                }
                
                $column['COLUMN_KEY']=='PRI';
                if($column['COLUMN_KEY']=='PRI'){
                    $cColumn->primaryKey=true;
                    $cTable->primaryKeys[]=$cColumn->columnName;
                }

                if($column['COLUMN_DEFAULT']!=''){
                    $cColumn->default=$column['COLUMN_DEFAULT'];
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


    function copyFolder($_currentFolder, $_destPath , $level=''){
        if ($_currentFolder == '') {
            $_currentFolder = realpath('./public/css/');
        }
        if(!file_exists($_destPath)){
            umask(000);
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
        $level.=' * ';
        foreach ($List as $pth) {
            if ($pth == '.' || $pth == '..') {
                continue;
            }
            $cPath = $_currentFolder . '/' . $pth;
            $dPath = $_destPath . '/' . $pth;
            if (is_dir($cPath)) {
                self::copyFolder($cPath, $dPath , $level) ;
            } elseif (is_file($cPath)) {
                $this->copyFile($cPath, $dPath);
            }else{
            }
        }
    }


    function copyFile($_origPath, $_destPath){
        if(!file_exists(dirname($_destPath))){
            umask(000);
            mkdir(dirname($_destPath),777,true);
        }
        if (is_file($_origPath )) {
            $tmpList = array(
                '{{NAMESPACE}}' => $this->config->getMVCNamespace(),
                '{{CLASS.PREFIX}}' => $this->config->getMVCPrefix()
            );
            $fileInfo = file_get_contents($_origPath);

            $view = strtr( $fileInfo , $tmpList);
            $mysBaseFile = fopen($_destPath, "w");
            fwrite($mysBaseFile, $view );
            fclose($mysBaseFile);
        }
    }

    

    function generateMVCFiles() {
        $this->copyFolder(__DIR__.'/templates/MVC', $this->config->getMVCPath().'src/MVC');
        $this->copyFile(__DIR__.'/templates/composer.template.tpl',$this->config->getMVCPath(). "composer.json");

        #public
        $this->copyFolder(__DIR__.'/templates/public/img/', $this->publicPath.'img');
        $this->copyFolder(__DIR__.'/templates/public/css/', $this->publicPath.'css');
        $this->copyFolder(__DIR__.'/templates/public/js/', $this->publicPath.'js');
        $this->copyFile(__DIR__.'/templates/public/index.php',$this->publicPath.'index.php');
        $this->copyFile(__DIR__.'/templates/public/.htaccess',$this->publicPath.'.htaccess');

        #controller
        $this->copyFile(__DIR__.'/templates/controller/DefaultController.php',$this->config->getMVCControllerPath(). $this->config->getMVCPrefix(). "DefaultController.php");

        # Views 
        $this->copyFolder(__DIR__.'/templates/views/core/', $this->config->getMVCViewsPath().'Core');
        $this->copyFile(__DIR__.'/templates/views/homepage.php',$this->config->getMVCViewsPath(). "homepage.php");


        return 'MVCFiles <span style="background:green; color:white; border-radius: 30px;"> &nbsp;&nbsp;created&nbsp;&nbsp; </span>';
    }

    function generateConfigFile() {

        $cfgTemplate = file_get_contents(__DIR__.'/templates/config.template.tpl');
        $tmpList = array(
            '{{MySQL.SERVER}}' => $this->config->getHostDB(),
            '{{DB.USER}}' => $this->config->getUsernameDB(),
            '{{DB.PASSWORD}}' => $this->config->getPasswordDB(),
            '{{DB.NAME}}' => $this->config->getNameDB(),
        );
        $classTmp = strtr( $cfgTemplate , $tmpList); 
        # Creating file:
        $modelFileName = "config.php";
        $myfile = fopen($this->config->getMVCPath().$modelFileName, "w");
        fwrite($myfile, $classTmp );
        fclose($myfile);

        return $modelFileName.' <span style="background:green; color:white; border-radius: 30px;"> &nbsp;&nbsp;created&nbsp;&nbsp; </span>';
    }

    function createMVCBase(){
        $baseHTML = '<div style="background: #dfdfdf;"> <span> MVC Base classes: </span> <br> <ul>';
        $baseHTML .= "<li> ".$this->generateConfigFile()."</li>";
        $baseHTML .= "<li> ".$this->generateMVCFiles()."</li>";
        $baseHTML.='</div>';
        return $baseHTML;
    }

    function generateModel(RYMTable $_table) {

        $model = new RYMModelGenerator($_table);
        
        $modelFileName = $model->getModelBaseFile() . ".php | " . $model->getModelFile().".php";
        if($model->generateModel()){
            return $modelFileName.' <span style="background:green; color:white; border-radius: 30px;"> &nbsp;&nbsp;created&nbsp;&nbsp; </span>';
        }else{
            return $modelFileName.' <span style="background:red; color:white; border-radius: 30px;"> &nbsp;&nbsp;creation failed&nbsp;&nbsp; </span>';
        }
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
      
        $controller = new RYMControllerGenerator($_table);
        
        $controllerFileName = $controller->getControllerFile();
        if($controller->generateController()){
            return $controllerFileName.' <span style="background:green; color:white; border-radius: 30px;"> &nbsp;&nbsp;created&nbsp;&nbsp; </span>';
        }else{
            return $controllerFileName.' <span style="background:red; color:white; border-radius: 30px;"> &nbsp;&nbsp;creation failed&nbsp;&nbsp; </span>';
        }

        return $controllerFileName.' <span style="background:green; color:white; border-radius: 30px;"> &nbsp;&nbsp;created&nbsp;&nbsp; </span>';
    }


    ###########################################################
    #                                                         # 
    #                       VIEWS                             #
    #                                                         #
    ###########################################################

       
    function generateViews(RYMTable $_table) {
      
        $view = new RYMViewsGenerator($_table);
        $viewFileName = $view->getViewFile();

        if($view->generateView()){
            return $viewFileName.' <span style="background:green; color:white; border-radius: 30px;"> &nbsp;&nbsp;created&nbsp;&nbsp; </span>';
        }else{
            return $viewFileName.' <span style="background:red; color:white; border-radius: 30px;"> &nbsp;&nbsp;creation failed&nbsp;&nbsp; </span>';
        }
    }
}
?>