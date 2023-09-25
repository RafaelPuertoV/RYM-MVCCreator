<?php

namespace {{NAMESPACE}}\MVC\Forms;

use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionException;
use ReflectionAttribute;
use ReflectionNamedType;
use ReflectionProperty;

class MVCForm extends MVCAbstractForm{

    private $formId;
    private $formTitle ;
    private $formElements;

    private $action;
    private $method;

    function set_action($_action){
        $this->action = $_action;
    }
    function get_action(){
        return $this->action;
    }
    function set_method($_method){
        $this->method = $_method;
    }
    function get_method(){
        return $this->method;
    }

    function set_formId($_formId){
        $this->formId = $_formId;
    }
    function get_formId(){
        return $this->formId;
    }

    public function __construct($modelObject=null) {
        $this->action='';
        $this->method='POST';
        $this->formId = 'custom-form';
        $this->formTitle = 'Form' ;
        $this->formElements = array();
        if(is_object($modelObject))
            $this->setFormByObject($modelObject);
       
    }

    public function setFormByObject($modelObject){
        $classObj =  get_class($modelObject);
        $reflection_class = new \ReflectionClass($classObj);
        $namespace = $reflection_class->getNamespaceName();

        $this->formId = 'custom-form';//str_replace($namespace.'\\','',$classObj) ;
        $this->formTitle = str_replace($namespace.'\\','', $classObj)  ;

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

            $attrInfo = $reflection_class->getProperty($column);
            $dbType = '';
            $dbOptions = [];
            $dbMaxlength =1024;

            $tmpList = array(
                '/**' => '',
                '*/' => '',
            );
            $tmp = strtr( $attrInfo->getDocComment() , $tmpList); 
            if(strlen($tmp)==0)
            {
                $dbType = 'varchar';
            }else{
                $dbAttribs = explode('*',$tmp);
                foreach ($dbAttribs as $dbAttrLine) {
                    if (trim($dbAttrLine)=='')
                    {
                        continue;
                    }
                    $dbAttr= explode(':',$dbAttrLine);
                    if (trim($dbAttr[0])=='DBType'){
                        $dbType = trim($dbAttr[1]);
                    }
                    if (trim($dbAttr[0])=='DBTypeLength'){
                        $dbMaxlength = trim($dbAttr[1]);
                    }
                    if (trim($dbAttr[0])=='ENUMOptions'){
                        $optList = explode(',',trim($dbAttr[1]));
                        foreach ($optList as $value) {
                            if($value ==''){
                                continue;
                            }
                            $value = str_replace("'","",$value);
                            $dbOptions[] = array(
                                'label'=>str_replace('_',' ',$value),
                                'value'=>$value
                            );
                        }
                    }
                }
            }
            // echo '<br><br><br><h3>'.$column.'</h3>';
            // var_dump($attrInfo->getDocComment());
            // echo '<br>dbType: ';var_dump($dbType);
            // echo '<br>dbMaxlength: ';var_dump($dbMaxlength);
            // echo '<br>dbOptions: ';var_dump($dbOptions);
            $htmlControl = '';
            switch($dbType)
            {
                case "tinyint":
                   $htmlControl = $this->entryText( $column, str_replace('_',' ',$column), $modelObject->$methodName() ,'', $dbMaxlength );
                   break;
                case "int":
                   $htmlControl = $this->entryText( $column, str_replace('_',' ',$column), $modelObject->$methodName() ,'', $dbMaxlength );
                   break;
                case "datetime":
                   $htmlControl = $this->calendar_datetime(str_replace('_',' ',$column), $column,  $modelObject->$methodName() ,  '' );
                   break;
                case "varchar":
                   $htmlControl = $this->entryText( $column, str_replace('_',' ',$column), $modelObject->$methodName() ,'', $dbMaxlength );
                   break;
                case "smallint":
                   $htmlControl = $this->entryText( $column, str_replace('_',' ',$column), $modelObject->$methodName() ,'', $dbMaxlength );
                   break;
                case "float":
                   $htmlControl = $this->entryText( $column, str_replace('_',' ',$column), $modelObject->$methodName() ,'', $dbMaxlength );
                   break;
                case "enum":
                   $htmlControl = $this->select( $column, str_replace('_',' ',$column), $dbOptions, $modelObject->$methodName() );
                   break;
                case "text":
                   $htmlControl = $this->entryText( $column, str_replace('_',' ',$column), $modelObject->$methodName() ,'', $dbMaxlength );
                   break;
                case "decimal":
                   $htmlControl = $this->entryText( $column, str_replace('_',' ',$column), $modelObject->$methodName() ,'', $dbMaxlength );
                   break;
                case "date":
                   $htmlControl = $this->calendar_date(str_replace('_',' ',$column), $column,  $modelObject->$methodName() ,  '' );
                   break;
                case "mediumint":
                   $htmlControl = $this->entryText( $column, str_replace('_',' ',$column), $modelObject->$methodName() ,'', $dbMaxlength );
                   break;
                case "mediumtext":
                   $htmlControl = $this->entryText( $column, str_replace('_',' ',$column), $modelObject->$methodName() ,'', $dbMaxlength );
                   break;
                case "year":
                   $htmlControl = $this->entryText( $column, str_replace('_',' ',$column), $modelObject->$methodName() ,'', $dbMaxlength );
                   break;
                case "char":
                   $htmlControl = $this->entryText( $column, str_replace('_',' ',$column), $modelObject->$methodName() ,'', $dbMaxlength );
                   break;
                default:
                    $htmlControl = $this->entryText( $column, str_replace('_',' ',$column), $modelObject->$methodName() ,'', $dbMaxlength );
                    break;
               
            }

            $this->formElements[] = array(
                'name' => $column,
                'label'=> str_replace('_',' ',$column),
                'dbType' => $dbType ,
                'dbOptions' => $dbOptions ,
                'dbMaxlength'=>$dbMaxlength,
                'valueType' => $getReturnType->getname(),
                'value' =>  $modelObject->$methodName(),
                'html' => $htmlControl,
            );
		}
    }

    public function getEditForm(){
        $form = '<form id="form-'.$this->formId.'" class="form-horizontal" action="'.$this->action.'" method="'.$this->method.'" >'
            . $this->formHeader( $this->formTitle  );
        foreach ($this->formElements as $key => $value) {
            $form.= $value['html'];
        }
        $form.=$this->formFooter($this->button('form-'.$this->formId.'_submit','Save','btn-primary', "onclick=\"apiUpdate('$this->formId')\""));
        return $form."</form>";
    }
}
