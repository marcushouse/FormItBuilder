<?php
require_once 'FormItBuilderCore.class.php';
/**
 * This class holds validation methods that are custom to FormItBuilder and not built into FormIt/jQuery Validate. Validate methods are called via a snippet in a roundabout way using globals to get around limitations using FormIt
 */
class FormItBuilder_customValidate extends FormItBuilderCore{
	
	/**
	 * Validates an element in a variety of ways that are not covered by FormIT
	 * @param string $value String to validate
	 * @param array $options Validation options passed as an associative array (must have a type element)
	 * @return array Returns an associative array with information on the validity of the value such as returnStatus(boolean), errorMsg(string), value(mixed) and extraInfo(mixed)
	 */
	public static function validateElement($value, array $options){
		if(empty($value)===true){
			return array('returnStatus'=>true,'errorMsg'=>NULL,'value'=>NULL,'extraInfo'=>NULL);
		}else{
			$returnStatus=true; //allow pass by default
			$errorMsg=NULL;
			$returnValue=$value;
			$returnExtraInfo=NULL;
			foreach($options as $option){
				switch($option['type']){
					case 'date':
						$a_formatInfo = FormItBuilder::is_valid_date($value, $option['fieldFormat']);
						$returnStatus = $a_formatInfo['status'];
						$returnValue = $a_formatInfo['value'];
						$returnExtraInfo = $a_formatInfo;
						if($returnStatus===false){
							$errorMsg = $option['errorMessage'];
						}
						break;
				}
			}
			return array('returnStatus'=>$returnStatus,'errorMsg'=>$errorMsg,'value'=>$returnValue,'extraInfo'=>$returnExtraInfo);
		}
	}
}
?>
