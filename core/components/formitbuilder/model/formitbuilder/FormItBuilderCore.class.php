<?php
require_once 'FormElement.class.php';
require_once 'FormItBuilder.class.php';
require_once 'FormRule.class.php';
/**
 * The core class for FormItBuilder every other class extends this class which allows easy access to the common static methods.
 */
class FormItBuilderCore{
	
	/**
	 * Setter function. This should always be overwritten, if not throw an error (forces extending classes to define setters)
	 * @param string $name Name of the property
	 * @param string $value Value of the property
	 */
	public function __set($name, $value){
		self::throwError('Attempt to set a non-existing property: '.$name.' with value '.$value);
	}
	
	/**
	 * Getter function. This should always be overwritten, if not throw an error (forces extending classes to define getters)
	 * @param string $name Name of the property
	 */
	public function __get($name){
		self::throwError('Attempt to get a non-existing property: '.$name);  
	}
	
	/**
	 * Testing function that verifies that the value is a boolean or throw an error.
	 * @param boolean $value Value to test if boolean
	 * @return boolean
	 */
	public static function forceBool($value){
		if(is_bool($value)===true){
			return $value;
		}else{
			self::throwError('Value "'.$value.'" must be type (bool) - true/false');
		}		
	}
	
	/**
	 * Testing function that verifies that the value is an integer or throw an error.
	 * @param integer $value Value to test if integer
	 * @return integer
	 */
	public static function forceNumber($value){
		if(is_int($value)===true){
			return $value;
		}else{
			self::throwError('Value "'.$value.'" must be an type (int) - (Pass only integer values not string numbers)');
		}		
	}
	
	/**
	 * Testing function that verifies that the value is an array or throw an error.
	 * @param array $value Value to test if array
	 * @return array
	 */
	public static function forceArray($value){
		if(is_array($value)===true){
			return $value;
		}else{
			self::throwError('Value "'.$value.'" must be type (array)');
		}		
	}
	
	/**
	 * Throws an error (probably should be logged to modx instead of an Exception, but makes for easier debugging).
	 * @param string $errorString String to output in the error
	 */
	public static function throwError($errorString){
		throw new Exception($errorString."\r\n");
	}
	
	/**
	 * Verify that the specified object is a valid form element object (if not an error is thrown).
	 * @param FormItBuilder_baseElement $element Element object
	 */
	public static function verifyFormElement(FormItBuilder_baseElement $element){
		if(is_a($element, 'FormItBuilder_baseElement')===false){
			self::throwError('Element "'.$element.'" is not a FormItBuilder_baseElement');
		}
	}
	
	/**
	 * Test if a string is a valid date against the specified format and return useful information about the date (such as a unix timestamp and processed version of the same date).
	 * @param string $value String to check for valid date format
	 * @param string $format Format must be a combination of dd mm yyyy in any order with a single character separator (default mm/dd/yyyy)
	 * @return array Returns an array of elements containing return status (Boolean status), processed date (String value) and timestamp (number) 
	 */
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
				$year = substr($value,$yearPos,4);
				$newStr=str_replace('yyyy',$year, $newStr);
				
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
