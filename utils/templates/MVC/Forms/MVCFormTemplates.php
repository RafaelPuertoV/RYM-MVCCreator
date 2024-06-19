<?php

namespace {{NAMESPACE}}\MVC\Forms;

class MVCFormTemplates{

    public static function  entryPassword(){
        return 
            '<div class="form-group">
                <label for="{{MVC_NAME}}" class="col-sm-2 control-label">{{MVC_LABEL}}</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" 
                        id="{{MVC_NAME}}"  
                        name="{{MVC_NAME}}" 
                        placeholder="{{MVC_PLACEHOLDER}}" 
                        value="{{MVC_VALUE}}" 
                        maxlength="{{MVC_MAXLENGTH}}" >
                </div>
            </div>';
    }

    public static function hidden(){

		return "<input name='{{MVC_NAME}}' id='{{MVC_NAME}}' type='hidden' value='{{MVC_VALUE}}'/>";
	}

    public static function calendar_date(){
        $elemenHTML = '<div class="form-group">
        <label for="{{MVC_NAME}}" class="col-sm-2 control-label">{{MVC_LABEL}}</label>
        <div class="col-sm-10">
            <div class="input-group">
                <input type="date" class="form-control" 
                    id="{{MVC_NAME}}"  
                    name="{{MVC_NAME}}" 
                    placeholder="{{MVC_PLACEHOLDER}}" 
                    value="{{MVC_VALUE}}" >
            </div>
        </div>
    </div>';
		return $elemenHTML;
	}

    public static function calendar_datetime(){
        $elemenHTML = '<div class="form-group">
        <label for="{{MVC_NAME}}" class="col-sm-2 control-label">{{MVC_LABEL}}</label>
        <div class="col-sm-10">
            <div class="input-group">
                <input type="datetime-local" class="form-control" 
                    id="{{MVC_NAME}}"  
                    name="{{MVC_NAME}}" 
                    placeholder="{{MVC_PLACEHOLDER}}" 
                    value="{{MVC_VALUE}}" >
            </div>
        </div>
    </div>';
		return $elemenHTML;
	}

    
	
	public static function entryText(){
		
		$elemenHTML = '<div class="form-group">
                <label for="{{MVC_NAME}}" class="col-sm-2 control-label">{{MVC_LABEL}}</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" 
                        id="{{MVC_NAME}}"  
                        name="{{MVC_NAME}}" 
                        placeholder="{{MVC_PLACEHOLDER}}" 
                        value="{{MVC_VALUE}}" 
                        maxlength="{{MVC_MAXLENGTH}}" 
                        "{{MVC_READONLY}}">
                </div>
            </div>';
		return $elemenHTML;
	}
        
	
	public static function textArea(){
        $elemenHTML = '<div class="form-group">
                <label for="{{MVC_NAME}}" class="col-sm-2 control-label">{{MVC_LABEL}}</label>
                <div class="col-sm-10">
                    <textarea class="form-control" 
                        id="{{MVC_NAME}}"  
                        name="{{MVC_NAME}}" 
                        rows="5">{{MVC_VALUE}}</textarea>
                </div>
            </div>';
		return $elemenHTML;
	}
	
	
	public static function file(){
        $elemenHTML ='<div class="form-group">
            <label for="{{MVC_NAME}}">{{MVC_LABEL}}</label>
            <input type="file" id="{{MVC_NAME}}">
        </div>';
		return $elemenHTML;
	}
	
    public static function select(int $totalOptions ){
		
        $elemenHTML = '<div class="form-group">
                <label for="{{MVC_NAME}}" class="col-sm-2 control-label">{{MVC_LABEL}}</label>
                <div class="col-sm-10">
                    <select class="form-control"  id="{{MVC_NAME}}" name="{{MVC_NAME}}" >';

        for ($idx=0; $idx < $totalOptions; $idx++) { 
            $elemenHTML .='<option value="{{MVC_OPTION'.$idx.'_VALUE}}"  {{MVC_OPTION'.$idx.'_SELECTED}} >{{MVC_OPTION'.$idx.'_LABEL}}</option>';
        }
        $elemenHTML.='    </select>
                </div>
            </div>';
		return $elemenHTML;
	}
	
	
	public static function autocomplete()
	{
		$elemenHTML = '<div class="form-group">
                <label for="{{MVC_NAME}}" class="col-sm-2 control-label">{{MVC_LABEL}}</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" 
                        id="{{MVC_NAME}}"  
                        name="{{MVC_NAME}}" 
                        value="{{MVC_VALUE}} >
                </div>
            </div>';
		return $elemenHTML;
	}
	
	public static function button(){
        return '<button type="button"  class="btn {{MVC_BTN_CLASS}} "  
            id="{{MVC_NAME}}"  
            name="{{MVC_NAME}}" {{MVC_EVENT}} >{{MVC_LABEL}}</button>';
	}
	
	public static function submitButton(){
        return '<button type="submit" class="btn {{MVC_BTN_CLASS}} " 
            id="{{MVC_NAME}}"  
            name="{{MVC_NAME}}" >{{MVC_LABEL}}</button>';
	}
	
	public static function choice($name, $label, $choices, $checked='' ){

        $elemenHTML = 
            '<div class="form-group">
                <label>
                    <input type="checkbox"> {{MVC_TITLE}}
                </label>
                <div class="col-sm-offset-2 col-sm-10">';
        
        for ($idx=0; $idx < count($totalOptions); $idx++) { 
            $elemenHTML ='<div class="checkbox">
                        <label>
                            <input type="radio"  
                                name="{{MVC_NAME}}" 
                                id="{{MVC_NAME}}_'.$idx.'" 
                                value="{{MVC_CHECK_'.$idx.'_VALUE}}" 
                                checked="{{MVC_CHECK_'.$idx.'_CHECKED}}" > 
                                    {{MVC_CHECK_'.$idx.'_LABEL}}
                                
                        </label>
                    </div>';
        }

        return $elemenHTML;
	}
	

	public static function check( int $totalOptions ){

        $elemenHTML = 
            '<div class="form-group">
                <label>
                    <input type="checkbox"> {{MVC_TITLE}}
                </label>
                <div class="col-sm-offset-2 col-sm-10">';
        
        for ($idx=0; $idx < count($totalOptions); $idx++) { 
            $elemenHTML ='<div class="checkbox">
                        <label>
                            <input type="checkbox"  
                                name="{{MVC_NAME}}" 
                                id="{{MVC_NAME}}_'.$idx.'" 
                                value="{{MVC_CHECK_'.$idx.'_VALUE}}" 
                                checked="{{MVC_CHECK_'.$idx.'_CHECKED}}" > 
                                    {{MVC_CHECK_'.$idx.'_LABEL}}
                        </label>
                    </div>';
        }

        $elemenHTML .='         </div>
            </div>';

		return $elemenHTML;
	}
}