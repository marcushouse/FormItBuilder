<?php
$snippetName='FormItBuilder_TestForm';
require_once $modx->getOption('core_path',null,MODX_CORE_PATH).'components/formitbuilder/model/formitbuilder/FormItBuilder.class.php';

/*--------------------*/
/*CREATE FORM ELEMENTS*/
/*--------------------*/
//Text Fields
$o_fe_checkMatrix          = new FormItBuilder_elementMatrix('checkMatrix','What foods do your children like?', 'check',
	array('Child 1','Child 2','Child 3','Child 4'),
	array('Fish','Beef','Chicken','Salad','Ice Cream')
);

$o_fe_radioMatrix          = new FormItBuilder_elementMatrix('radioMatrix','How do you feel about us?', 'radio',
	array('Service Quality','Overall Hygiene','Responsiveness','Kindness and Helpfulness'),
	array('Very Satisfied','Satisfied','Somewhat Satisfied','Not Satisfied')
);

$o_fe_textMatrix = new FormItBuilder_elementMatrix('textMatrix','List your favorite websites', 'text',
	array('Website #1','Website #2','Website #3','Website #4', 'Website #5'),
	array('Site Name','URL','Speed','Design')
);

$o_fe_buttSubmit	= new FormItBuilder_elementButton('submit','Submit Form','submit');


/*--------------------*/
/*SET VALIDATION RULES*/
/*--------------------*/
$a_formRules=array();
//Set required fields
$a_formFields_required = array($o_fe_checkMatrix,$o_fe_radioMatrix,$o_fe_textMatrix);
foreach($a_formFields_required as $field){
	$a_formRules[] = new FormRule(FormRuleType::required,$field);
}

/*----------------------------*/
/*CREATE FORM AND ADD ELEMENTS*/
/*----------------------------*/
$o_form = new FormItBuilder($modx,'TestForm');
$o_form->setHooks(array('spam','email','redirect'));
//$o_form->setRedirectDocument(4); //document to redirect to after successfull submission
$o_form->addRules($a_formRules);
$o_form->setPostHookName($snippetName);

$o_form->setEmailToAddress('your@email.address');
$o_form->setEmailToName('Your Name');
$o_form->setEmailFromAddress('your@email.address');

$o_form->setEmailSubject('MyCompany Contact Form Submission');
$o_form->setEmailHeadHtml('<p>This is a response sent using the contact us form:</p>');


$o_form->setJqueryValidation(true);

//add elements to form in preferred order
$o_form->addElements(
	array($o_fe_checkMatrix,$o_fe_radioMatrix,$o_fe_textMatrix, $o_fe_buttSubmit)
);


if(isset($hook)===true){
	//this same snippet was called via various other hooks
	return $o_form->processCoreHook($hook,$o_form);
}else{
	//Final output for form
	return $o_form->output();
}