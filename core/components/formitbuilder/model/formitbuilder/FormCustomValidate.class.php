<?php
require_once 'FormItBuilderCore.class.php';
class FormItBuilder_customValidate extends FormItBuilderCore{
	
	public static function validateElement(string $key, string $value, array $options){
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
						$errorMsg = str_replace('===dateformat===',$option['fieldFormat'], $option['errorMessage']);
					}else{
						
					}
					break;
			}
		}
		return array('returnStatus'=>$returnStatus,'errorMsg'=>$errorMsg,'value'=>$returnValue,'extraInfo'=>$returnExtraInfo);
	}
}
?>
