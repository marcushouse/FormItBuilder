<?php
require_once 'FormItBuilderCore.class.php';

class FormItBuilder extends FormItBuilderCore{
	/**
	 * A reference to the modX instance
	 * @var modX $modx
	 */
	private $modx;

	private $_method;
	private $_id;
	private $_redirectDocument;
	private $_jqueryValidation;
	private $_formElements;
	private $_postHookName;
	private $_headHtml;
	private $_emailFromAddress;
	private $_emailSubject;
	private $_emailFontSize;
	private $_emailFontFamily;
	private $_emailHeadHtml;
	private $_rules;

	/**
	*
	* @param modX $modx Reference to the core modX object
	* @param string $id Id of the form
	*/
	function __construct(modX &$modx, string $id) {
	    $this->modx = &$modx;
		$this->_method = 'post'; 
		$this->_id = $id;
		$this->_formElements=array();
		$this->_rules=array();
		$this->_redirectDocument=$this->modx->resource->get('id');
		$this->_jqueryValidation=false;
		
		$this->_emailFontSize='13px';
		$this->_emailFontFamily='Helvetica,Arial,sans-serif';
	}
	
	public function addRule(FormRule $formRule){
		if(is_a($formRule,'FormRule')===false){
			FormItBuilder::throwError('Form rule "'.$formRule.'" is not a valid FormRule type. Recommend using FormRuleType constants to define rule type.');
		}else{
			$this->_rules[]=$formRule;
		}
	}
	
	public function addRules($rules){
		foreach($rules as $rule){
			$this->addRule($rule);
		}
	}
	

	
	//getters & setters
	public function getMethod() { return $this->_method; } 
	public function getId() { return $this->_id; } 
	public function getRedirectDocument() { return $this->_redirectDocument; } 
	public function getJqueryValidation() { return $this->_jqueryValidation; } 
	public function getPostHookName() { return $this->_postHookName; }
	public function getEmailAddress() { return $this->_emailFromAddress; }
	public function getEmailSubject() { return $this->_emailSubject; }
	public function getEmailHeadHtml() { return $this->_emailHeadHtml; }
	
	public function setMethod($v) { $this->_method = $v; } 
	public function setRedirectDocument($v) { $this->_redirectDocument = $v; } 
	public function setJqueryValidation($v) { $this->_jqueryValidation = self::forceBool($v); }
	public function setPostHookName($v) { $this->_postHookName = $v; }
	public function setEmailFromAddress($v) { $this->_emailFromAddress = $v; }
	public function setEmailSubject($v) { $this->_emailSubject = $v; }
	public function setEmailHeadHtml($v) { $this->_emailHeadHtml = $v; }
        
	public function addElement(FormItBuilder_element $o_formElement){
		$this->_formElements[]=$o_formElement;
	}
	public function addElements($a_elements){
		foreach($a_elements as $o_formElement){
			$this->addElement($o_formElement);
		}
	}
	public function postHook(){
		$s_style = 'font-size:'.$this->_emailFontSize.'; font-family:'.$this->_emailFontFamily.';';
		
		$s_ret='<div style="'.$s_style.'">'.$this->_emailHeadHtml
		.'<table style="'.$s_style.'">';
		
		$cnt=0;
		foreach($this->_formElements as $o_el){
			if(get_class($o_el)=='FormItBuilder_htmlBlock'){
				//do nothing
			}else{
				if($o_el->showInEmail()===true){
					$s_ret.='<tr valign="top"><td><b>'.htmlentities($o_el->getLabel()).':</b></td><td>[[+'.htmlentities($elID).']]</td></tr>';
				}
			}
		}

		$s_ret.='</table>'
		.'<p>You can use this link to reply: <a href="mailto:'.htmlentities($this->_emailFromAddress).'?subject=RE:'.htmlentities($this->_emailSubject).'">'.htmlentities($this->_emailFromAddress).'</a></p>'
		.'</div>';
		return $s_ret;
	}
	private function jqueryValidateJSON($jqFieldProps,$jqFieldMessages,$jqFormRules,$jqFormMessages){
		$a_ruleSegs = array();
		$a_msgSegs = array();
		foreach($jqFieldProps as $fieldID=>$a_fieldProp){
			if(count($a_fieldProp)>0){
				$a_ruleSegs[]=$fieldID.':{'.implode(',',$a_fieldProp).'}';
			}
		}
		foreach($jqFieldMessages as $fieldID=>$a_fieldMsg){
			if(count($a_fieldMsg)>0){
				$a_msgSegs[]=$fieldID.':{'.implode(',',$a_fieldMsg).'}';
			}
		}
		/*
		foreach($jqFieldMessages as $a_formMsg){
			$a_ruleSegs[]=$a_formMsg;
			if(count($jqFormMessages)>0){
				$a_msgSegs[]=$a_formMsg;
			}
		}
		foreach($jqFormRules as $a_formProp){
			$a_ruleSegs[]=$a_formProp;
		}
		foreach($jqFormMessages as $a_formMsg){
			$a_ruleSegs[]=$a_formProp;
			if(count($jqFormMessages)>0){
				$a_msgSegs[]=$a_formMsg;
			}
		}
		*/
		$s_js=
		'rules:{  '."\r\n  ".implode(",\r\n  ",$a_ruleSegs)."\r\n".'},'.
		'messages:{  '."\r\n  ".implode(",\r\n  ",$a_msgSegs)."\r\n".'}'
		;
		
		return $s_js;
	}

	public function output(){
		$s_submitVar = 'submitVar_'.$this->_id;
		$nl="\r\n";
		$s_form='<form action="[[~'.$this->modx->resource->get('id').']]" method="'.htmlentities($this->_method).'" class="form" id="'.htmlentities($this->_id).'"><div>'.$nl
		.$nl.'<input type="hidden" name="'.$s_submitVar.'" value="1" />'
		.$nl.'<input type="hidden" name="fke'.date('Y').'Sp'.date('m').'Blk:blank" value="" /><!-- additional crude spam block. If this field ends up with data it will fail to submit -->'
		.$nl;

		//process and add form element rules
		foreach($this->_formElements as $o_el){
			if(is_a($o_el,'FormItBuilder_htmlBlock')){
				$s_form.=$o_el->outputHTML();
			}else{
				
				/*			
				$a_fieldProps[$o_el->getId()]=array();
				$a_fieldProps_jqValidate[$o_el->getId()]=array();
				
				//add required first so that required warnings are displayed before any other.
				if($o_el->isRequired()===true){
					$a_fieldProps[$o_el->getId()][] = 'required';
					$a_fieldProps_jqValidate[$o_el->getId()][] = 'required:true';
				}
				//textfield validators
				if(is_a($o_el,'FormItBuilder_elementText')){
					if($o_el->isNumeric()===true){
						$a_fieldProps[$o_el->getId()][] = 'isNumber';
						$a_fieldProps_jqValidate[$o_el->getId()][] = 'digits:true';
					}
					if($o_el->isEmail()===true){
						$a_fieldProps[$o_el->getId()][] = 'email';
						$a_fieldProps_jqValidate[$o_el->getId()][] = 'email:true';
					}
					
					$minLen=$o_el->getMinLength();
					$maxLen=$o_el->getMaxLength();
					if($minLen!==NULL){
						$a_fieldProps[$o_el->getId()][] = 'minLength=`'.$minLen.'`';
						$a_fieldProps_jqValidate[$o_el->getId()][] = 'minlength:'.$minLen;
					}
					if($maxLen!==NULL){
						$a_fieldProps[$o_el->getId()][] = 'maxLength=`'.$maxLen.'`';
						$a_fieldProps_jqValidate[$o_el->getId()][] = 'maxlength:'.$maxLen;
					}
					
					$minVal=$o_el->getMinValue();
					$maxVal=$o_el->getMaxValue();
					if($minVal!==NULL){
						$a_fieldProps[$o_el->getId()][] = 'minValue=`'.$minVal.'`';
						$a_fieldProps_jqValidate[$o_el->getId()][] = 'min:'.$minVal;
					}
					if($maxVal!==NULL){
						$a_fieldProps[$o_el->getId()][] = 'maxValue=`'.$maxVal.'`';
						$a_fieldProps_jqValidate[$o_el->getId()][] = 'max:'.$maxVal;
					}
				}
				 */

				$s_form.='<div class="formSegWrap formSegWrap_'.htmlentities($o_el->getId()).'">';
					if($o_el->showLabel()===true){
						$s_form.=$nl.'  <label for="'.htmlentities($o_el->getId()).'">'.htmlentities($o_el->getLabel()).'</label>';
					}
					$s_form.=$nl.'  <div class="elWrap">'.$nl.'    '.$o_el->outputHTML();
					if($o_el->showLabel()===true){
						$s_form.=$nl.'  <label class="nonjqValidate error" for="'.htmlentities($o_el->getId()).'">[[+fi.error.'.htmlentities($o_el->getId()).']]</label>';
					}
					$s_form.=$nl.'  </div>';
				$s_form.=$nl.'</div>'.$nl;
			}
		}
		$s_form.=$nl.'</div></form>';
		
				
		//process and add form rules
		$a_fieldProps=array();
		$a_fieldProps_jqValidate=array();
		$a_fieldProps_errstringFormIt=array();
		$a_fieldProps_errstringJq=array();
		
		$a_formProps=array();
		$a_formProps_jqValidate=array();
		$a_formPropsFormItErrorStrings=array();
		$a_formPropsJqErrorStrings=array();

		foreach($this->_rules as $rule){
			$o_elFull = $rule->getElement();
			if(is_array($o_elFull)===true){
				$o_el = $o_elFull[0];
			}else{
				$o_el = $o_elFull;
			}
			$elID = $o_el->getId();
			
			if(isset($a_fieldProps[$elID])===false){
				$a_fieldProps[$elID]=array();
			}
			if(isset($a_fieldProps_jqValidate[$elID])===false){
				$a_fieldProps_jqValidate[$elID]=array();
			}
			if(isset($a_fieldProps_errstringFormIt[$elID])===false){
				$a_fieldProps_errstringFormIt[$elID]=array();
			}
			if(isset($a_fieldProps_errstringJq[$elID])===false){
				$a_fieldProps_errstringJq[$elID]=array();
			}
			
			switch($rule->getType()){
				case FormRuleType::email:
					$a_fieldProps[$elID][] = 'email';
					$a_fieldProps_jqValidate[$elID][] = 'email:true';
					$a_fieldProps_errstringFormIt[$elID][] = 'vTextEmailInvalid=`'.$rule->getValidationMessage().'`';
					$a_fieldProps_errstringJq[$elID][] = 'email:"'.$rule->getValidationMessage().'"';
					break;
				case FormRuleType::fieldMatch:
					$a_fieldProps[$elID][] = 'password_confirm=`'.$o_elFull[1]->getId().'`';
					$a_fieldProps_jqValidate[$elID][] = 'equalTo:"#'.$o_elFull[1]->getId().'"';
					$a_fieldProps_errstringFormIt[$elID][] = 'vTextPasswordConfirm=`'.$rule->getValidationMessage().'`';
					$a_fieldProps_errstringJq[$elID][] = 'equalTo:"'.$rule->getValidationMessage().'"';
					break;
				case FormRuleType::maximumLength:
					$a_fieldProps[$elID][] = 'maxLength=`'.$rule->getValue().'`';
					$a_fieldProps_jqValidate[$elID][] = 'maxlength:'.$rule->getValue();
					$a_fieldProps_errstringFormIt[$elID][] = 'vTextMaxLength=`'.$rule->getValidationMessage().'`';
					$a_fieldProps_errstringJq[$elID][] = 'maxlength:"'.$rule->getValidationMessage().'"';
					break;
				case FormRuleType::maximumValue:
					$a_fieldProps[$elID][] = 'maxValue=`'.$rule->getValue().'`';
					$a_fieldProps_jqValidate[$elID][] = 'max:'.$rule->getValue();
					$a_fieldProps_errstringFormIt[$elID][] = 'vTextMaxValue=`'.$rule->getValidationMessage().'`';
					$a_fieldProps_errstringJq[$elID][] = 'max:"'.$rule->getValidationMessage().'"';
					break;
				case FormRuleType::minimumLength:
					$a_fieldProps[$elID][] = 'minLength=`'.$rule->getValue().'`';
					$a_fieldProps_jqValidate[$elID][] = 'minlength:'.$rule->getValue();
					$a_fieldProps_errstringFormIt[$elID][] = 'vTextMinLength=`'.$rule->getValidationMessage().'`';
					$a_fieldProps_errstringJq[$elID][] = 'minlength:"'.$rule->getValidationMessage().'"';
					break;
				case FormRuleType::minimumValue:
					$a_fieldProps[$elID][] = 'minValue=`'.$rule->getValue().'`';
					$a_fieldProps_jqValidate[$elID][] = 'min:'.$rule->getValue();
					$a_fieldProps_errstringFormIt[$elID][] = 'vTextMinValue=`'.$rule->getValidationMessage().'`';
					$a_fieldProps_errstringJq[$elID][] = 'min:"'.$rule->getValidationMessage().'"';
					break;
				case FormRuleType::numeric:
					$a_fieldProps[$elID][] = 'isNumber';
					$a_fieldProps_jqValidate[$elID][] = 'digits:true';
					$a_fieldProps_errstringFormIt[$elID][] = 'vTextIsNumber=`'.$rule->getValidationMessage().'`';
					$a_fieldProps_errstringJq[$elID][] = 'digits:"'.$rule->getValidationMessage().'"';
					break;
				case FormRuleType::required:
					$a_fieldProps[$elID][] = 'required';
					$a_fieldProps_jqValidate[$elID][] = 'required:true';
					$a_fieldProps_errstringFormIt[$elID][] = 'vTextRequired=`'.$rule->getValidationMessage().'`';
					$a_fieldProps_errstringJq[$elID][] = 'required:"'.$rule->getValidationMessage().'"';
					break;

			}
		}		
		
		//add all formit validation rules together in one array for easy implode
		$a_formItCmds=array();
		$a_formItErrorMessage=array();
		foreach($a_fieldProps as $fieldID=>$a_fieldProp){
			if(count($a_fieldProp)>0){
				$a_formItCmds[]=$fieldID.':'.implode(':',$a_fieldProp);
				//$a_formItErrorMessage[]=something';
			}
		}
		for($i=0; $i<count($a_formProps); $i++){
			$a_formItCmds[]=$a_formProps[$i];
			if(empty($a_formPropsFormItErrorStrings[$i])===false){
				$a_formItErrorMessage[]=$a_formPropsFormItErrorStrings[$i];
			}
		}
		$s_formItCmd='[[!FormIt?'
		.$nl.'&hooks=`'.$this->_postHookName.',spam,email,redirect`'
		.$nl.'&emailTpl=`FormItBuilderEmailTpl`'
		.$nl.'&emailTo=`marcus@datawebnet.com.au`'
		.(isset($this->_emailFromAddress)?$nl.'&emailFrom=`'.$this->_emailFromAddress.'`':'')
		.$nl.'&emailSubject=`'.$this->_emailSubject.'`'
		.$nl.'&emailUseFieldForSubject=`1`'
		.$nl.'&redirectTo=`'.$this->_redirectDocument.'`'
		.$nl.'&submitVar=`'.$s_submitVar.'`'
		.$nl.implode($nl,$a_formItErrorMessage)
		.$nl.'&validate=`'.$nl.' '.implode(','.$nl.' ',$a_formItCmds).$nl.'`]]'.$nl;
		
		if($this->_jqueryValidation===true){
			$s_js='
<script type="text/javascript">
// <![CDATA[
$().ready(function() {

$(".nonjqValidate").remove();
$("#'.$this->_id.'").validate({ignore:":hidden",'.
					
$this->jqueryValidateJSON(
	$a_fieldProps_jqValidate,
	$a_fieldProps_errstringJq,
	$a_formProps_jqValidate,
	$a_formPropsJqErrorStrings
).'});

});
// ]]>
</script>
';
		}
		
		//for debugging
		//echo $s_formItCmd.$s_form; exit();
		
		return $s_formItCmd.$s_form.$s_js;
		
		return '[[!FormIt?
	&hooks=`email,redirect`
	&emailTpl=`sent_email_template`
	&emailTo=`marcus@datawebnet.com.au`
	&emailSubject=`Email Subject`
	&emailUseFieldForSubject=`1`
	&redirectTo=`11`
	&submitVar=`contactform`
	&validate=`
		contact_name:required,
        contact_company:required,
        contact_address:required,
        contact_city:required,
        contact_state:required,
        contact_postcode:required,
        contact_country:required,
		contact_email:email:required,
        contact_phone:required`
  ]]
  
  [[!+fi.error.error_message:notempty=`<p class="error">[[!+fi.error.error_message]]</p>`]]
  
  <h1>Product Enquiry / Quote</h1>
  
  <p><strong>Please submit only ONE form at a time.</strong><br /></p>
            
  <form action="[[~[[*id]]]]" method="post" id="product-enquiry-form">
    
      <input type="hidden" name="contactform" value="1" />
	  
	  <!-- additional crude spam block. If this field ends up with data it will fail to submit -->
	  <input type="hidden" name="fkeSpBlk:blank" value="" />
  
      <p><label for="contact_name">Full Name * <span class="error">[[+fi.error.contact_name]]</span></label>
      <input type="text" name="contact_name" id="contact_name" maxlength="60" value="[[+fi.contact_name]]" /></p>
      
      <p><label for="contact_company">Company * <span class="error">[[+fi.error.contact_company]]</span></label>
      <input type="text" name="contact_company" id="contact_company" maxlength="60" value="[[+fi.contact_company]]" /></p>

      <p><label for="contact_position">Position</label>
      <input type="text" name="contact_position" id="contact_position" maxlength="60" value="[[+fi.contact_position]]" /></p>

      <p><label for="contact_address">Postal Address *<span class="error">[[+fi.error.contact_address]]</span></label>
      <input type="text" name="contact_address" id="contact_address" maxlength="60" value="[[+fi.contact_address]]" /></p>

      <p><label for="contact_city">City * <span class="error">[[+fi.error.contact_city]]</span></label>
      <input type="text" name="contact_city" id="contact_city" maxlength="60" value="[[+fi.contact_city]]" /></p>

      <p><label for="contact_state">State * <span class="error">[[+fi.error.contact_state]]</span></label>
      <input type="text" name="contact_state" id="contact_state" maxlength="60" value="[[+fi.contact_state]]" /></p>
      
      <p><label for="contact_postcode">Zip / Postcode * <span class="error">[[+fi.error.contact_postcode]]</span></label>
      <input type="text" name="contact_postcode" id="contact_postcode" maxlength="60" value="[[+fi.contact_postcode]]" /></p>
      
      <p><label for="contact_country">Country * <span class="error">[[+fi.error.contact_country]]</span></label>
      <input type="text" name="contact_country" id="contact_country" maxlength="60" value="[[+fi.contact_country]]" /></p>            
  
      <p><label for="contact_email">Email * <span class="error">[[+fi.error.contact_email]]</span></label>
      <input type="text" name="contact_email" id="contact_email" size="40" maxlength="40" value="[[+fi.contact_email]]" /></p>
      
      <p><label for="contact_phone">Phone</label>
      <input type="text" name="contact_phone" id="contact_phone" maxlength="60" value="[[+fi.contact_phone]]" /></p>      
      
      <p><label for="contact_best-time">Best contact time</label>
      <input type="text" name="contact_best-time" id="contact_best-time" maxlength="60" value="[[+fi.contact_best-time]]" /></p>

      <p><label for="contact_request-quote">Send me a quotation on</label>
      <input type="text" name="contact_request-quote" id="contact_request-quote" maxlength="60" value="[[+fi.contact_request-quote]]" /></p>

      <p><label for="contact_request-information">Send me information about</label>
      <input type="text" name="contact_request-information" id="contact_request-information" maxlength="60" value="[[+fi.contact_request-information]]" /></p>
  
      <p><label for="contact_text">Questions / Comments</label>
      <textarea cols="40" rows="4" name="contact_text" id="contact_text">[[+fi.contact_text]]</textarea></p>
  
     <p><input type="image" name="submit" class="submit" alt="Submit" src="assets/images/global/submit.png" /></p>
  
  </form>
  ';
	}
}
?>