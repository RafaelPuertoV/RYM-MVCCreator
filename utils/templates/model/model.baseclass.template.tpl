<?php
/**
 * Base Model 
 *
 * @category   Pro
 * @package    RYMMVCCreator
 * @author     Rafael Puerto <rafael.puerto.v@gmail.com>
 * @link       https://github.com/RafaelPuertoV/RYMMVCCreator
 * @see        RYMMCreator
 */

namespace {{NAMESPACE}}\Models\Base;

use {{NAMESPACE}}\MVC\MVCDataBase;


class {{CLASS.PREFIX}}{{CLASS.NAME}}Base implements \JsonSerializable
{
    static $table = "{{TABLE.NAME.LOWCASE}}";

    static $primarykey = {{TABLE.PRIMARYKEYS.ARRAY}}

    static $db = null;
    
    {{ATTRIBUTES.LIST}}

    public function __construct()
    {
        self::$db = new MVCDataBase();
    }

    {{ATTRIBUTES.GET.SET}}

    public static function findBy($_parameters, $_options = null )
    {
        if(!is_array($_parameters)){
            return null;
        }
        $optionQ ='';
        $limit='';
        if(is_array($_options)){
            foreach($_options as $optionKey => $optionValue){
                switch(strtolower($optionKey)){
                    case 'limit':
                        $limit = ' LIMIT '.$optionValue;
                        break;
                    case 'page':
                        $maxEntriesPerPage = 30;
                        $elementToBegin = ($optionValue * $maxEntriesPerPage) - $maxEntriesPerPage;
                        $limit = " LIMIT " . $elementToBegin . ", " . $maxEntriesPerPage ." ";
                        break;
                }
            }
        }
        
        $whereQ = '';
        $whereParameters = array();
        foreach($_parameters as $column => $columnValue){
            $whereParameters['param'.$column] = $columnValue;
            if($whereQ!=''){
                $whereQ.=' AND ';
            }
            $whereQ.=$column.'=:param'.$column;
        }
        if($whereQ!=''){
            $whereQ = ' WHERE '.$whereQ;
        }
        self::$db = new MVCDataBase();
        $objList = self::$db->queryObject("SELECT * 
            FROM ".self::$table." 
            " . $whereQ . $limit, $whereParameters ,__CLASS__);

        if (count($objList) > 0) {
            return $objList; 
        }else{
            return null;
        }
    }

    public static function all( $_options = null) 
    {
       return self::findBy(array(),$_options);
    }

    public function delete() 
    {
        $whereQ = '';
        $whereParameters = array();
        foreach(self::$primarykey as $column ){
            $getMethod= "get_".$column;
            $whereParameters['param'.$column] =  $this->$getMethod();
            if($whereQ!=''){
                $whereQ.=' AND ';
            }
            $whereQ.=$column.'=:param'.$column;
        }
        $delete = self::$db->query("DELETE FROM ".self::$table."
            WHERE ".$whereQ,$whereParameters);
    }


    public function create() 
    {
        $parameters = get_class_vars(__CLASS__);

        $columns='';
        $columnsValues = '';
        $whereParameters = array();
        foreach($parameters as $column ){
            if($column == 'table' || 
                $column == 'primarykey' || 
                $column == 'db' ){
               continue;
            }
            $getMethod= "get_".$column;
            $whereParameters['param'.$column] =  $this->$getMethod();
            if($columns!=''){
                $columns.=' , ';
                $columnsValues.=' , ';
            }
            $columns.=$column;
            $columns.= ':param'.$column;
        }
        $delete = self::$db->query("INSERT INTO ".self::$table."
            (".$columns.") VALUES
            (".$columnsValues.");"
            ,$whereParameters);
    }

    public function update() 
    {
$parameters = get_class_vars(get_class());
        $whereQ = '';
        $whereParameters = array();
        foreach(self::$primarykey as $column ){
            $getMethod= "get_".$column;
            $whereParameters['param'.$column] =  $this->$getMethod();
            if($whereQ!=''){
                $whereQ.=' AND ';
            }
            $whereQ.=$column.'=:param'.$column;
        }

        $setColumnValues = '';
        foreach($parameters as $column => $columnValue  ){
            if( $column =='' || 
                $column == 'table' || 
                $column == 'primarykey' || 
                $column == 'db' ||
                $column == "sQuery" ||
                $column == "settings" ||
                $column == "bConnected" ||
                $column == "logPath" ||
                $column == "parameters" || 
                isset($whereParameters['param'.$column]) ){
                continue;
            }
            $getMethod= "get_".$column;
            $whereParameters['param'.$column] =  $this->$getMethod();
            if($setColumnValues!=''){
                $setColumnValues.=' , ';
            }

            $setColumnValues.= $column.'=:param'.$column;
        }
        $update = self::$db->query("UPDATE ".self::$table."
            SET ".$setColumnValues."
            WHERE ".$whereQ ,
            $whereParameters);
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}