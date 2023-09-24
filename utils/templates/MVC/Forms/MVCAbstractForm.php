<?php

namespace {{NAMESPACE}}\MVC\Forms;

class MVCAbstractForm{

	public function __construct(){

	}
	/*
	 */
	protected function formHeader($header){

		return "<tr>\t\n<td colspan='2' id='headerTitle' align='center'>\t\n<h2>$header</h2>\t\n</td>\t\n</tr>\t\n";
	}
	
	
	protected function hidden($name, $value){

		return "<input name='$name' id='$name' type='hidden' value='$value'/>";
	}
	
	
	protected function calendar($label, $name, $title='', $value=''){
			
		$elemenHTML ="<tr><td class='formHead'><strong><label>$label</label></strong><img title='$title' src='{{WEB.ROOT}}img/help.gif'/></td></tr>";
		$elemenHTML .="<tr><td class='inputRow'> <input class='form-control' type='date' id='_$name' name='$name' value='$value'/></td></tr>";

		return $elemenHTML;
	}
	
	protected function entryText($label, $name, $event = "", $title = "", $value='', $readonly='', $class='form-control' , $placeholder='',$disabled=''){
			
		$elemenHTML = '';
		if($title!=''){
				
			$elemenHTML .="<tr><td class='formHead'><strong><label>$label</label></strong> <img title='$title' src='{{WEB.ROOT}}img/help.gif'/></td></tr>";	
		}else{
			$elemenHTML .="<tr><td class='formHead'><strong><label>$label</label></strong></td></tr>";
		}
                if($disabled!=''){
                    $dis = "disabled";
                }else{
                    $dis="";
                }
		if($readonly!=''){
				
			$elemenHTML .="<tr><td class='inputRow'><input class='$class' type='text' id='_$name' name='$name' value='$value' readonly='true' $dis /></td></tr>";
		}else{
			$elemenHTML .="<tr><td class='inputRow'><input class='$class' type='text' id='_$name' name='$name' placeholder='$placeholder' value='$value' $dis /></td></tr>";
			$elemenHTML .="<tr><td><label class='info' id='inf_$name' value=''></label></td></tr>";
		}
		return $elemenHTML;
	}

	protected function entryTextConf($label, $name, $event = "", $title = "", $value='', $readonly='', $class='form-control' , $placeholder=''){
		
		$elemenHTML = '';
		if($title!=''){
				
			$elemenHTML .="<tr><td class='formHead'><strong><label>$label</label></strong> <img title='$title' src='{{WEB.ROOT}}img/help.gif'/></td></tr>";	
		}else{
			$elemenHTML .="<tr><td class='formHead'><strong><label>$label</label></strong></td></tr>";
		}
		if($readonly!=''){
				
			$elemenHTML .="<tr><td class='inputRow'><input class='$class' type='text' id='_conf$name' name='$name' value='$value' readonly='true'/></td></tr>";
		}else{
			$elemenHTML .="<tr><td class='inputRow'><input class='$class' type='text' id='_conf$name' name='$name' placeholder='$placeholder' value='$value'/></td></tr>";
			$elemenHTML .="<tr><td><label class='info' id='inf_$name' value=''></label></td></tr>";
		}
		return $elemenHTML;
	}
        
	
	protected function textArea($label, $name, $value='', $title=''){

		$elemenHTML ="<tr><td class='formHead'><strong><label>$label</label></strong> <img title='$title' src='{{WEB.ROOT}}img/help.gif'/></td></tr>";
		$elemenHTML .="<tr><td class='inputRow'><textarea id='_$name' name='$name' cols='40' rows='5'>$value</textarea></td></tr>";
		return $elemenHTML;
	}
	
	
	protected function textAreaDoubleRow($label, $name, $value = '', $title='') {

		$elemenHTML ="<tr><td class='formHead'><strong><label>$label</label></strong> <img title='$title' src='{{WEB.ROOT}}img/help.gif'/></td></tr>";
		$elemenHTML .="<tr><td class='inputRow'><textarea id='$name' name='$name' cols='40' rows='5'>$value</textarea></td></tr>";
		return $elemenHTML;
	}
	
	protected function _file($label, $name, $title='',$class=''){

		$elemenHTML ="<tr><td class='formHead'><strong><label>$label</label></strong> <img title='$title' src='{{WEB.ROOT}}img/help.gif'/></td></tr>";
		$elemenHTML .="<tr><td class='inputRow'><input type='file' id='_$name' class='$class' name='$name' /></td></tr>";
		return $elemenHTML;
	}
	
	protected function entryPassword($label, $size, $name){

		$elemenHTML ="<tr>\t\n<td class='formColumn label-column'>\t\n";
		$elemenHTML .="<strong class='tstrong'>$label:</strong>\t\n</td>\t\n";
		$elemenHTML .="<td class='formColumn value-column'>\t\n";
		$elemenHTML .="<input type='password' name='$name' maxlength='$size'/>\t\n</td>\t\n</tr>\t\n";
		return $elemenHTML;
	}
	
	protected function entryPasswordTwoRows($label, $size, $name, $class='form-control'){
	
		$elemenHTML ="<tr><td class='formHead'><strong><label>$label</label></strong> </td></tr>";
		$elemenHTML .="<tr><td class='inputRow'><input type='password' class='$class' name='$name' maxlength='$size'/></td></tr>";
		return $elemenHTML;
	}
	
	
	protected function select($label, $name, $event, $options, $selected='', $disabled='', $title='', $class=''){
			
		$elemenHTML ="<tr><td class='formHead'><strong><label id='lbl$name'>$label</label></strong><img id='img$name' title='$title' src='{{WEB.ROOT}}img/help.gif'/></td></tr>";
		$elemenHTML .="<tr><td class='inputRow'>";
    	if($event!='' && $disabled==''){

			$elemenHTML .="<select id='_$name' name='$name' class='$class' onchange=\"".$event."\">\t\n";
		}elseif($disabled!=''){
				
			$elemenHTML .="<select id='_$name' class='$class' disabled='disabled'' name='$name' onchange=\"".$event."\">\t\n";
		}else{
			$elemenHTML .="<select id='_$name' class='$class' name='$name' >\t\n";
		}
		foreach($options as $option){
				
			if($option['label']===$selected || $option['value']==$selected){
			
				$elemenHTML .="<option value='".$option['value']."' selected='selected'>".$option['label']."</option>\t\n";
			}else{
				$elemenHTML .="<option value='".$option['value']."'>".$option['label']."</option>\t\n";
			}
		}
    	$elemenHTML .="</select></td></tr>";
		return $elemenHTML;
	}
	
	
	protected function autocomplete($label, $name,$value='')
	{
		
		$elemenHTML = "<div class=\"ui-widget\">";
		
		$elemenHTML .= "<label for=\"_".$name."\">".$label.": </label>";
		$elemenHTML .= "<input id=\"_".$name."\" name=\"".$name."\" />";
		$elemenHTML .= "</div>";
		return $elemenHTML;
	}
	
	
	protected function autocompleteTwoColumns($label, $name,$valuename='',$valueid='', $class='')
	{	
		$elemenHTML ="<tr><td class='formHead'>";
		$elemenHTML .= "<div id=\"".$name."_ui_label_wrapper\">";
		$elemenHTML .="<strong><label for=\"".$name."_ui\">$label</label></strong>";
		$elemenHTML .= "<tr><td><label id='info'>*seleccione una opci&oacute;n si aparece en la lista</label></td></tr>";
		$elemenHTML .="</div>";
		$elemenHTML .="</td></tr>";
		$elemenHTML .="<tr><td class='inputRow'>";
		$elemenHTML .= "<div id=\"".$name."_ui_input_wrapper\">";
		$elemenHTML .= "<div class=\"ui-widget\">";
		$elemenHTML .= "<input type='text' id=\"".$name."_ui\" name=\"".$name."_ui_name\" value=\"".$valuename."\" class=\"".$class."\" />";
		$elemenHTML .= "</div>";
		$elemenHTML .= "</div>";
		$elemenHTML .="</td></tr>";
		$elemenHTML .= "</div>";
		$elemenHTML .= $this->hidden($name,$valueid);
		return $elemenHTML;
	}
	
	
	protected function button($value, $event){
		$elemenHTML ="<tr>\t\n<td colspan='2' align='center'>\t\n";
		$elemenHTML .="<button type='button' class='btn btn-primary'  onclick='$event'><span >".$value."</span></button>";
		$elemenHTML .="\t\n</td>\t\n</tr>\t\n";
		return $elemenHTML;
	}
	
	protected function submitButton($value){
		$elemenHTML ="<tr>\t\n<td colspan='2'><center>\t\n";
		$elemenHTML .="<button type='submit' class='btn btn-primary'><span >".$value."</span></button>";
		$elemenHTML .="\t\n</center></td>\t\n</tr>\t\n";
		return $elemenHTML;
	}
	
	protected function choice($label, $choices, $name, $checked='', $title='', $event=''){
		$elemenHTML ="<tr><td class='formHead'><strong><label>$label</label></strong> <img title='$title' src='{{WEB.ROOT}}img/help.gif'/></td></tr>";
		$elemenHTML .="<td class='formColumn value-column'>\t\n";
		foreach($choices as $choice){

			if($checked===$choice['value']){
					
				$elemenHTML .="<input onclick=\"$event('".$choice['value']."')\" class='radio' checked='checked' name='$name' type='radio' value='".$choice['value']."' onclick=\"".$choice['event']."\"/>".$choice['label'] . "<br><br>";
			}else{
				$elemenHTML .="<input onclick=\"$event('".$choice['value']."')\" class='radio' name='$name' type='radio' value='".$choice['value']."' onclick=\"".$choice['event']."\"/>".$choice['label'] . "<br><br>";
			}
		}
		$elemenHTML .="\t\n</td>\t\n";
		return $elemenHTML;
	}
	

	protected function check($name, $label, $options, $title='', $values=array()){
		
		if(is_string($values))
			$values=explode('|', $values);
		$elemenHTML ="<tr><td class='formHead'><strong><label>$label</label></strong> <img title='$title' src='{{WEB.ROOT}}img/help.gif'/></td></tr>";
		$elemenHTML .="<tr><td class='formColumn value-column'>";
		foreach($options as $option){
			
			if(in_array($option['value'], $values)){
				
				$elemenHTML .="<input class='check' type='checkbox' id='$name' name='$name' value='".$option['value']."' checked>".$option['label']."<br>";
			}else{
				$elemenHTML .="<input class='check' type='checkbox' id='$name' name='$name' value='".$option['value']."'>".$option['label']."<br>";
			}
		}
		$elemenHTML .="</td></tr>";
		return $elemenHTML;
	}
	
}
?>