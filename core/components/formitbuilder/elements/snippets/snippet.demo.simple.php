<?php
$snippetName='FormItBuilder_BasicExample';
require_once $modx->getOption('core_path',null,MODX_CORE_PATH).'components/formitbuilder/model/formitbuilder/FormItBuilder.class.php';
if (function_exists('FormItBuilder_BasicExample')===false) {
function FormItBuilder_BasicExample(modX &$modx, $snippetName) { 
 
     
     
     
//CREATE FORM ELEMENTS
$o_fe_name      = new FormItBuilder_elementText('name_full','Your Name');
$o_fe_email     = new FormItBuilder_elementText('email_address','Email Address');
$o_fe_notes     = new FormItBuilder_elementTextArea('comments','Comments',5,30);
$o_fe_buttSubmit    = new FormItBuilder_elementButton('submit','Submit Form','submit');
 
//SET VALIDATION RULES
$a_formRules=array();
//Set required fields
$a_formFields_required = array($o_fe_notes, $o_fe_name, $o_fe_email);
foreach($a_formFields_required as $field){
    $a_formRules[] = new FormRule(FormRuleType::required,$field);
}
//make email field require a valid email address
$a_formRules[] = new FormRule(FormRuleType::email, $o_fe_email, NULL, 'Please provide a valid email address');
 
//CREATE FORM AND SETUP
$o_form = new FormItBuilder($modx,'contactForm');
$o_form->setHooks(array('spam','email','redirect'));
$o_form->setRedirectDocument(5);
$o_form->addRules($a_formRules);
$o_form->setPostHookName($snippetName);
$o_form->setEmailToAddress('your@email.address');
$o_form->setEmailFromAddress('[[+email_address]]');
$o_form->setEmailSubject('FormItBuilder Contact Form Submission - From: [[+name_full]]');
$o_form->setEmailHeadHtml('<p>This is a response sent by [[+name_full]] using the contact us form:</p>');
$o_form->setJqueryValidation(true);
 
//ADD ELEMENTS TO THE FORM IN PREFERRED ORDER
$o_form->addElements(
    array(
        $o_fe_name,$o_fe_email,$o_fe_notes,
        new FormItBuilder_htmlBlock('<hr class="formSpltter" />'),
        $o_fe_buttSubmit
    )
);
 
return $o_form;
     
 
 
 
}
}
//Run the form construction function above
$o_form = FormItBuilder_BasicExample($modx,$snippetName);
if(isset($outputType)===false){
    //this same snippet was called via the email posthook
    $hook->setValue('FormItBuilderEmailTpl',$o_form->postHook());
    return true;
}else{
    //Final output for form
    return $o_form->output();
}
?>