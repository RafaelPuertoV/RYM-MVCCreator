<?php

class RYMColumn
{
    public $columnName = '';
    public $primaryKey = false;
    public $DBType = '';
    public $DBTypeLength = 0;
    public $ENUMOptions = '';
    public $default;


    public function getPHPType(){
        /*
        # https://www.php.net/manual/en/language.types.type-system.php
        Built-in types
            null type
            Scalar types:
            bool type
            int type
            float type
            string type*/
        $type_map = array( 
            "tinyint" => 'int',
            "int"=>  "int" ,
            "datetime"=>  "string" ,
            "varchar"=>  "string" ,
            "smallint"=>  "int" ,
            "float"=>  "float" ,
            "enum"=>  "string" ,
            "text"=>  "string" ,
            "decimal"=>  "float" ,
            "date"=>  "string" ,
            "mediumint"=>  "int" ,
            "mediumtext"=>  "string" ,
            "year"=>  "int" ,
            "char"=>  "string" ,
        );
        
        if(array_key_exists($this->DBType, $type_map) ) {
            return $type_map[$this->DBType];
        }else{
            return 'text';
        }
    }

    public function getPHPDefault(){

        switch ($this->getPHPType()){
            case "int":
                if(is_null($this->default) || $this->default==''){
                    $this->default=0;
                }
                return $this->default;
            case  "string" :
                if(is_null($this->default) || $this->default==''){
                    $this->default='';
                }
                return "'".$this->default."'";
            case  "float" :
                if(is_null($this->default) || $this->default==''){
                    $this->default=0;
                }
                return $this->default;
            default:
                if(is_null($this->default) || $this->default==''){
                    $this->default='';
                }
                return $this->default;
        }
    }
}
