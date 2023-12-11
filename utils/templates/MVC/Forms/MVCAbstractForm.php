<?php

namespace {{NAMESPACE}}\MVC\Forms;
use {{NAMESPACE}}\MVC\Forms\MVCFormTemplates;

class MVCAbstractForm{

	public function __construct(){

	}

	protected function show($label, $name,  $value=''){
		return '<div class="form-group">
			<label for="'.$name.'" class="col-sm-2 control-label">'.$label.'</label>
			<div class="col-sm-10">
				<div class="form-control" id="'.$name.'" > '.$value.'</div>
			</div>
		</div>';
	}
	
	/*
	 */
	protected function formHeader($header){

		return '<div class="text-center"> <h2> '.$header .'</h2></div>';
	}
	protected function formFooter($html){

		return '<div class="text-center"> <h2> '.$html .'</h2></div>';
	}
	
	
	protected function hidden($name, $value){
		$tmpList = array(
			'{{MVC_NAME}}' => $name,
			'{{MVC_VALUE}}'=> $value
		);
			
        $elemenHTML = strtr( MVCFormTemplates::hidden() , $tmpList); 
        
		return $elemenHTML ;
	}
	
	
	protected function calendar_date($label, $name,  $value='',  $placeholder = '' ){
		$tmpList = array(
			'{{MVC_NAME}}' => $name,
			'{{MVC_LABEL}}' => $label,
			'{{MVC_VALUE}}'=> $value,
			'{{MVC_PLACEHOLDER}}' => $placeholder
		);
			
        $elemenHTML = strtr( MVCFormTemplates::calendar_date() , $tmpList); 

		return $elemenHTML;
	}

	protected function calendar_datetime($label, $name,  $value='',  $placeholder = '' ){
		$tmpList = array(
			'{{MVC_NAME}}' => $name,
			'{{MVC_LABEL}}' => $label,
			'{{MVC_VALUE}}'=> $value,
			'{{MVC_PLACEHOLDER}}' => $placeholder
		);
			
        $elemenHTML = strtr( MVCFormTemplates::calendar_datetime() , $tmpList); 

		return $elemenHTML;
	}
	
	protected function entryText( $name, $label, $value='',$placeholder='', $maxLength = 1024 , $readonly=''){
		
		$tmpList = array(
			'{{MVC_NAME}}' => $name,
			'{{MVC_LABEL}}' => $label,
			'{{MVC_VALUE}}'=> $value,
			'{{MVC_PLACEHOLDER}}' => $placeholder,
			'{{MVC_MAXLENGTH}}' => $maxLength,
			'{{MVC_READONLY}}' => $readonly 
		);

        $elemenHTML = strtr( MVCFormTemplates::entryText() , $tmpList); 

		return $elemenHTML;
	}
	
	protected function textArea($name,  $label, $value='' ){

		$tmpList = array(
			'{{MVC_NAME}}' => $name,
			'{{MVC_LABEL}}' => $label,
			'{{MVC_VALUE}}'=> $value
		);

        $elemenHTML = strtr( MVCFormTemplates::textArea() , $tmpList); 
		
		return $elemenHTML;
	}
	
	
	protected function _file($name, $label ){
		$tmpList = array(
			'{{MVC_NAME}}' => $name,
			'{{MVC_LABEL}}' => $label,
		);

        $elemenHTML = strtr( MVCFormTemplates::file() , $tmpList); 
		return $elemenHTML;
	}
	
	protected function entryPassword($name, $label, $maxLength ){

		$tmpList = array(
			'{{MVC_NAME}}' => $name,
			'{{MVC_LABEL}}' => $label,
			'{{MVC_VALUE}}'=> $value,
			'{{MVC_PLACEHOLDER}}' => $placeholder,
			'{{MVC_MAXLENGTH}}' => $maxLength,
			'{{MVC_READONLY}}' => $readonly 
		);

        $elemenHTML = strtr( MVCFormTemplates::entryPasswords() , $tmpList); 

		return $elemenHTML;
	}
	
	
	protected function select( $name, $label, $options, $selected='' ){
		
		$tmpList = array(
			'{{MVC_NAME}}' => $name,
			'{{MVC_LABEL}}' => $label,
		);

		$idx=0;
		foreach($options as $option){
			$tmpList['{{MVC_OPTION'.$idx.'_LABEL}}'] = $option['label'];
			$tmpList['{{MVC_OPTION'.$idx.'_VALUE}}'] = $option['value'];
			$tmpList['{{MVC_OPTION'.$idx.'_SELECTED}}']= $option['value']==$selected ? 'selected':'';

			$idx++;
		}
		
		
		$elemenHTML = strtr( MVCFormTemplates::select(count($options)) , $tmpList); 

		return $elemenHTML;
	}
	
	
	protected function autocomplete($label, $name,$value='')
	{
		$tmpList = array(
			'{{MVC_NAME}}' => $name,
			'{{MVC_LABEL}}' => $label,
			'{{MVC_VALUE}}'=> $value,
		);

        $elemenHTML = strtr( MVCFormTemplates::autocomplete() , $tmpList); 

		return $elemenHTML;
	}
	
	
	protected function button($name, $label, $class='btn-primary' ,$event=''){
		$tmpList = array(
			'{{MVC_NAME}}' => $name,
			'{{MVC_LABEL}}' => $label,
			'{{MVC_BTN_CLASS}}'=> $class,
			'{{MVC_EVENT}}' => $event,
		);

        $elemenHTML = strtr( MVCFormTemplates::button() , $tmpList); 
		return $elemenHTML;
	}
	
	protected function submitButton($name, $label, $class='btn-primary'){
		$tmpList = array(
			'{{MVC_NAME}}' => $name,
			'{{MVC_LABEL}}' => $label,
			'{{MVC_BTN_CLASS}}'=> $class
		);

        $elemenHTML = strtr( MVCFormTemplates::submitButton('') , $tmpList); 
		return $elemenHTML;
	}
	
	protected function choice($name, $title='Options', $choices,  $checked='' ){

		$tmpList = array(
			'{{MVC_NAME}}' => $name,
			'{{MVC_TITLE}}' => $title,
		);

		$idx=0;
		foreach($options as $option){
			$tmpList['{{MVC_CHECK_'.$idx.'_LABEL}}'] = $option['label'];
			$tmpList['{{MVC_CHECK_'.$idx.'_VALUE}}'] = $option['value'];
			$tmpList['{{MVC_CHECK_'.$idx.'_CHECKED}}']= $option['value']==$selected ? 'checked':'';

			$idx++;
		}
		
		$elemenHTML = strtr( MVCFormTemplates::choice(count($options)) , $tmpList); 

		return $elemenHTML;
	}
	

	protected function check( $name, $title='Options', $choices,  $checked='' ){
		
		if(is_string($options))
			$values=explode('|', $values);

		$tmpList = array(
			'{{MVC_NAME}}' => $name,
			'{{MVC_TITLE}}' => $title,
		);

		$idx=0;
		foreach($options as $option){
			$tmpList['{{MVC_CHECK_'.$idx.'_LABEL}}'] = $option['label'];
			$tmpList['{{MVC_CHECK_'.$idx.'_VALUE}}'] = $option['value'];
			$tmpList['{{MVC_CHECK_'.$idx.'_CHECKED}}']= $option['value']==$selected ? 'checked':'';

			$idx++;
		}
		
		$elemenHTML = strtr( MVCFormTemplates::check(count($options)) , $tmpList); 
		
		return $elemenHTML;
	}
	
}
?>