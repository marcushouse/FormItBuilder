<?php
$a_store = $GLOBALS['FormItBuilder_customValidation'];
if(isset($a_store[$key])){
	require_once $modx->getOption('core_path',null,MODX_CORE_PATH).'components/formitbuilder/model/formitbuilder/FormCustomValidate.class.php';
	$a_res = FormItBuilder_customValidate::validateElement($key, $value, $a_store[$key]);
	//Some functions may auto tidy or re-format the value. If so replace current value in formIT fields array.
	if(empty($validator->fields[$key])===false && empty($a_res['value'])===false){
		$validator->fields[$key] = $a_res['value'];
	}

	if($a_res['returnStatus']===false){
		$validator->addError($key,$a_res['errorMsg']);
	}
}
//if no fails, return true
return true;
?>
