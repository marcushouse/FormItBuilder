<?php
require_once 'FormElement.class.php';
require_once 'FormItBuilder.class.php';
require_once 'FormRule.class.php';

class FormItBuilderCore{
	//not allow any unknown properties from get/set
	public function __set($name, $value){
		self::throwError('Attempt to set a non-existing property: '.$name.' with value '.$value);
	}  
	public function __get($name){
		self::throwError('Attempt to get a non-existing property: '.$name);  
	}
	
	public static function forceBool($v){
		if(is_bool($v)===true){
			return $v;
		}else{
			self::throwError('Value "'.$v.'" must be type (bool) - true/false');
		}		
	}
	public static function forceNumber($v){
		if(is_int($v)===true){
			return $v;
		}else{
			self::throwError('Value "'.$v.'" must be an type (int) - (Pass only integer values not string numbers)');
		}		
	}
	public static function forceArray($v){
		if(is_array($v)===true){
			return $v;
		}else{
			self::throwError('Value "'.$v.'" must be type (array)');
		}		
	}
	public static function throwError($errorString){
		throw new Exception($errorString."\r\n");
	}
	public static function verifyFormElement(FormItBuilder_element $el){
		if(is_a($el, 'FormItBuilder_element')===false){
			self::throwError('Element "'.$el.'" is not a FormItBuilder_element');
		}
	}

}

?>
