<?php


class RYMTable
{
    public $tableName ;
    public $columns = array();

    public $primaryKeys = array();
    
    public $foreingKeys = array() ;


    function getModelNameClass(){
        /*$tmp1 = str_replace('_',' ', $_tableName );
        echo '<br>str_replace: '. $tmp1;
        $tmp2=ucwords( $tmp1);
        echo '<br>ucwords: '. $tmp2;
        $tmp3= str_replace(' ', '', $tmp2);
        echo '<br>str_replace: '. $tmp3;*/
        return str_replace(' ', '', ucwords( str_replace('_',' ', $this->tableName ) ) );
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



