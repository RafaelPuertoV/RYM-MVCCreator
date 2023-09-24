<?php

namespace {{NAMESPACE}}\MVC\Forms;

use ReflectionMethod;
use ReflectionFunction;
use ReflectionException;
use ReflectionAttribute;
use ReflectionNamedType;

class MVCForm extends MVCAbstractForm{

    private $formTitle ;
    private $formElements;
    public function __construct($modelObject) {
        $classObj =  get_class($modelObject) ;
        $this->formTitle = $classObj  ;

        $this->formElements = array();
        
		$parameters = json_decode(json_encode($modelObject));

        $columns='';
        $columnsValues = '';
        $whereParameters = array();
        foreach($parameters as $column => $value ){
            if($column == 'table' || 
                $column == 'primarykey' || 
                $column == 'db' ){
               continue;
            }
            $methodName = "get_".$column;
            $geColumnMethod = new ReflectionMethod($modelObject,$methodName);
            $getReturnType = $geColumnMethod->getReturnType();
            /*
            echo '<br><br>geColumnMethod: ';var_dump($geColumnMethod);
            echo '<br><br>getReturnType):';var_dump($getReturnType);
            echo '<br><br>getParameters:';var_dump($geColumnMethod->getParameters());
            echo '<br><br>hasReturnType:';var_dump($geColumnMethod->hasReturnType());
            echo '<br><br>getReturnType:';var_dump($geColumnMethod->getReturnType()->getname());
            */

            $this->formElements[] = array(
                'name' => $column,
                'valueType' => $getReturnType->getname(),
                'value' =>  $modelObject->$methodName(),
                'html' => '',
            );
		}
    }

    public function getForm(){
        $form = "<table width='100%' border='0' class='table'>"
            . $this->formHeader( $this->formTitle  );
        foreach ($this->formElements as $key => $value) {
            $form.= $this->entryText($value['name'], $value['name'] , '', '', $value['value']);
        }
        $form.=$this->submitButton('Save');
        return $form."</table>";
    }
}

	