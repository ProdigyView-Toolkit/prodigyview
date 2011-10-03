<?php
/*
*Copyright 2011 ProdigyView LLC. All rights reserved.
*
*Redistribution and use in source and binary forms, with or without modification, are
*permitted provided that the following conditions are met:
*
*   1. Redistributions of source code must retain the above copyright notice, this list of
*      conditions and the following disclaimer.
*
*   2. Redistributions in binary form must reproduce the above copyright notice, this list
*      of conditions and the following disclaimer in the documentation and/or other materials
*      provided with the distribution.
*
*THIS SOFTWARE IS PROVIDED BY ProdigyView LLC ``AS IS'' AND ANY EXPRESS OR IMPLIED
*WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
*FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL ProdigyView LLC OR
*CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
*CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
*SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
*ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
*NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
*ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
*The views and conclusions contained in the software and documentation are those of the
*authors and should not be interpreted as representing official policies, either expressed
*or implied, of ProdigyView LLC.
*/
class PVForms extends PVStaticObject {
	
	public static function input($name, $type, $options=array(), $css_options=array()){
		
		$input='<input name="'.$name.'" type="'.$type.'" ';
		
		$input.=PVHtml::getStandardAttributes($options);
		$input.=PVHtml::getEventAttributes($options);
		$input.=self::getFormAttributes($options);
		
		$input.='/>';
		
		if(!isset($css_options['disable_css'])){
			return PVHtml::div($input, $css_options);
		}
		
		return	$input;
		
	}
	
	public static function button($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-button');
		$css_options += $css_defaults;
		
		return self::input($name, 'button', $options, $css_options);
	}
	
	public static function checkbox($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-checkbox');
		$css_options += $css_defaults;
		
		return self::input($name, 'checkbox', $options, $css_options);
	}
	
	public static function text($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-text');
		$css_options += $css_defaults;
		
		return self::input($name, 'text', $options, $css_options);
	}
	
	public static function file($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-file');
		$css_options += $css_defaults;
		
		return self::input($name, 'file', $options, $css_options);
	}
	
	
	public static function date($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-date');
		$css_options += $css_defaults;
		
		return self::input($name, 'date', $options, $css_options);
	}
	
	
	public static function hidden($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-hidden');
		$css_options += $css_defaults;
		
		return self::input($name, 'hidden', $options, $css_options);
	}
	
	public static function image($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-image');
		$css_options += $css_defaults;
		
		return self::input($name, 'image', $options, $css_options);
	}
	
	
	public static function search($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-search');
		$css_options += $css_defaults;
		
		return self::input($name, 'search', $options, $css_options);
	}
	
	public static function submit($name, $value='Submit', $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-submit');
		$css_options += $css_defaults;
		$options['value']=$value;
		
		return self::input($name, 'submit', $options, $css_options);
	}
	
	
	public static function textfield($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-textfield');
		$css_options += $css_defaults;
		
		return self::input($name, 'text', $options, $css_options);
	}
	
	public static function radiobutton($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-radio');
		$css_options += $css_defaults;
		
		return self::input($name, 'radio', $options, $css_options);
	}
	
	public static function time($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-time');
		$css_options += $css_defaults;
		
		return self::input($name, 'time', $options, $css_options);
	}
	
	public static function url($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-url');
		$css_options += $css_defaults;
		
		return self::input($name, 'url', $options, $css_options);
	}
	
	public static function range($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-range');
		$css_options += $css_defaults;
		
		return self::input($name, 'range', $options, $css_options);
	}
	
	public static function reset($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-reset');
		$css_options += $css_defaults;
		
		return self::input($name, 'reset', $options, $css_options);
	}
	
	public static function color($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-color');
		$css_options += $css_defaults;
		
		return self::input($name, 'color', $options, $css_options);
	}
	
	public static function password($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-password');
		$css_options += $css_defaults;
		
		return self::input($name, 'password', $options, $css_options);
	}
	
	public static function number($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-number');
		$css_options += $css_defaults;
		
		return self::input($name, 'number', $options, $css_options);
	}
	public static function email($name, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-email');
		$css_options += $css_defaults;
		
		return self::input($name, 'email', $options, $css_options);
	}

	
	public static function label($text, $options=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-label');
		$css_options += $css_defaults;
		
		$label='<label ';
		
		if(!empty($options['for'])){
			$label.='for="'.$options['for'].'" ';
		}
		
		$label.=PVHtml::getStandardAttributes($options);
		$label.=PVHtml::getEventAttributes($options);
		$label.=self::getFormAttributes($options);
		
		$label.='>'.$text.'</label>';
		
		if(!isset($css_options['disable_css'])){
			return PVHtml::div($label, $css_options);
		}
		
		return	$label;
	}
	
	public static function select($name, $data, $options=array(), $css_options=array()){
		$css_defaults=array('name'=>'form-select');
		$css_options += $css_defaults;	
			
		$tag='<select name="'.$name.'" ';
		$tag.=PVHtml::getStandardAttributes($options);
		$tag.=PVHtml::getEventAttributes($options);
		$tag.=self::getFormAttributes($options);
		$tag.='>';
		
		foreach($data as $key=>$value){
			if(is_array($value)){	
				$tag.='<option ';
				$tag.=PVHtml::getStandardAttributes($value);
				$tag.=PVHtml::getEventAttributes($value);
				$tag.=self::getFormAttributes($value);
				$tag.='>'.$value['option'].'</option>';
			} else {
				if(isset($options['value'])){
					if(is_array($options['value'])){
						if(in_array($key, $options['value'])){
							$tag.='<option value="'.$key.'" selected >'.$value.'</option>';
						} else {
							$tag.='<option value="'.$key.'" >'.$value.'</option>';
						}
					} else {
						if($key==$options['value']){
							
							$tag.='<option value="'.$key.'" selected >'.$value.'</option>';
						} else {
							$tag.='<option value="'.$key.'" >'.$value.'</option>';
						}
					}//end iset value
				} else {
					$tag.='<option value="'.$key.'" >'.$value.'</option>';
				}
			}
		}
		
		$tag.='</select>';
		
		if(!isset($css_options['disable_css'])){
			return PVHtml::div($tag, $css_options);
		}
		
		return	$tag;
	}
	
	public static function getFormAttributes($attributes=array()){
		$return_attributes='';
		$accepted_attributes=array('accept', 'autocomplete', 'autofocus', 'checked', 'disabled', 'form', 'formaction','formenctype', 'formmethod', 'formnovalidate', 'formtarget', 'height', 'list', 'max', 'maxlength', 'min', 'multiple', 'pattern', 'placeholder', 'readonly', 'required', 'size', 'step', 'type', 'value', 'width', 'novalidate', 'dirname');
		
		foreach($attributes as $key => $attribute){
			if(in_array($key, $accepted_attributes) && !PVValidator::isInteger($key)){
				$return_attributes.=$key.'="'.$attribute.'" ';
			}
		}
		
		return $return_attributes;
	}
	
	public static function textarea($name, $value, $attributes=array(), $css_options=array()){
		$css_defaults=array('class'=>'form-textarea');
		$css_options += $css_defaults;
		
		$textarea='<textarea ';
		
		$textarea.='name="'.$name.'" ';
		
		$textarea.=PVHtml::getStandardAttributes($attributes);
		$textarea.=PVHtml::getEventAttributes($attributes);
		$textarea.=self::getFormAttributes($attributes);
		
		$textarea.='>'.$value.'</textarea>';
		
		if(!isset($css_options['disable_css'])){
			return PVHtml::div($textarea, $css_options);
		}
		
		return	$textarea;
		
	}//end getTextArea
	
	public static function form($name, $data, $options=array()){
		$tag=self::formBegin($name, $options);
		$tag.=$data;
		$tag.=self::formClose();
		
		return $tags;
		
	}
	
	public static function formBegin($name, $options=array()){
		$defaults=array('method'=>'POST');
		$options += $defaults;
		
		$input='<form ';
		
		if(!empty($options['method'])){
			$input.='method="'.$options['method'].'" ';
		}
		
		if(!empty($options['name'])){
			$input.='name="'.$options['name'].'" ';
		}
		
		if(!empty($options['accept-charset'])){
			$input.='accept-charset="'.$options['accept-charset'].'" ';
		}
		
		if(!empty($options['action'])){
			$input.='action="'.$options['action'].'" ';
		}
		
		if(!empty($options['enctype'])){
			$input.='enctype="'.$options['enctype'].'" ';
		}

		$input.=PVHtml::getStandardAttributes($options);
		$input.=PVHtml::getEventAttributes($options);
		$input.=self::getFormAttributes($options);
		
		$input.='>';
		
		return	$input;
	}

	public static function formEnd($options=array()){
		$input='</form>';
		
		return	$input;
	}
	
}//end class
	