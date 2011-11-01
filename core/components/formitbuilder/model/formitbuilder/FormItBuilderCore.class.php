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
	
	public static function is_valid_date($value, $format = 'mm/dd/yyyy'){
		$b_retStatus=false;
		$s_retValue='';
		$n_retTimestamp=0;
		if(strlen($value)==strlen($format)){
			// find separator. Remove all other characters from $format 
			$separator_only = str_replace(array('m','d','y'),'', $format); 
			$separator = $separator_only[0]; // separator is first character 
			if($separator && strlen($separator_only) == 2){
				
				$newStr = $format;
				
				$dayPos = strpos($format,'dd');
				$day = substr($value,$dayPos,2);
				$newStr=str_replace('dd',$day, $newStr);
				
				$monthPos = strpos($format,'mm');
				$month = substr($value,$monthPos,2);
				$newStr=str_replace('mm',$month, $newStr);
				
				$yearPos = strpos($format,'yyyy');
				if($yearPos===false){
					//if cant find yyyy assume 2 digit year
					$yearPos = strpos($format,'yy');
					$year = substr($value,$yearPos,2);
					$newStr=str_replace('yy',$year, $newStr);
				}else{
					$year = substr($value,$yearPos,4);
					$newStr=str_replace('yyyy',$year, $newStr);
				}
				
				if(@checkdate($month, $day, $year)){
					$b_retStatus=true;
					$s_retValue=$newStr;
					$n_retTimestamp=strtotime($year.'-'.$month.'-'.$year);
				}
			} 
		}
		return array('status'=>$b_retStatus,'value'=>$s_retValue,'timestamp'=>$n_retTimestamp);
	} 

}

?>
