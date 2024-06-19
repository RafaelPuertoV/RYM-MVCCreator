<?php


class RYMTable
{
    public $tableName ;
    public $columns = array();

    public $primaryKeys = array();
    
    public $foreingKeys = array() ;

    public function __construct($tableName){
        $this->tableName = $tableName;
    }

    function getPrimaryKeysTemplate(){

        $items = '';
        foreach ($this->primaryKeys as $fk) {
            if($items!='')
                $items.=", " ;
            $items.="'".$fk."'";
        }
        return "array(".$items.");";
    }
}



