<?php
/**
 * Contains the classes used for the main FormItBuilder object.
 * @package FormItBuilder
 */

/**
 * Required Files
 */
require_once dirname(__FILE__).'/FormItBuilderCore.class.php';

/**
 * The main FormItBuilder methods. Most of the program bulk lives within this class and handles a great number of set/get methods and output methods.
 * @package FormItBuilder
 */
class FormItBuilder extends FormItBuilderCore{
	/**
	 * @ignore 
	 */
	private $modx;
	/**
	 * @ignore 
	 */
	private $_method;
	/**
	 * @ignore 
	 */
	private $_id;
	/**
	 * @ignore 
	 */
	private $_redirectDocument;
	/**
	 * @ignore 
	 */
	private $_redirectParams;
	/**
	 * @ignore 
	 */
	private $_hooks;
	/**
	 * @ignore 
	 */
	private $_jqueryValidation;
	/**
	 * @ignore 
	 */
	private $_formElements;
	/**
	 * @ignore 
	 */
	private $_postHookName;
	/**
	 * @ignore 
	 */
	private $_emailFromAddress;
	/**
	 * @ignore 
	 */
	private $_emailSubject;
	/**
	 * @ignore 
	 */
	private $_emailToAddress;
	/**
	 * @ignore 
	 */
	private $_emailFontSize;
	/**
	 * @ignore 
	 */
	private $_emailFontFamily;
	/**
	 * @ignore 
	 */
	private $_emailHeadHtml;
	/**
	 * @ignore 
	 */
	private $_emailFootHtml;
	/**
	 * @ignore 
	 */
	private $_rules;
	/**
	 * @ignore 
	 */
	private $_emailTpl;
	/**
	 * @ignore 
	 */
	private $_validate;
	/**
	 * @ignore 
	 */
	private $_customValidators; 
	/**
	 * @ignore 
	 */
	private $_databaseTableObjectName;
	/**
	 * @ignore 
	 */
	private $_databaseTableFieldMapping;
	/**
	 * @ignore 
	 */
	private $_store;
	/**
	 * @ignore 
	 */
	private $_placeholderJavascript;
	/**
	 * @ignore 
	 */
	private $_emailFromName;
	/**
	 * @ignore 
	 */
	private $_emailToName;
	/**
	 * @ignore 
	 */
	private $_emailReplyToAddress;
	/**
	 * @ignore 
	 */
	private $_emailReplyToName;
	/**
	 * @ignore 
	 */
	private $_emailCCAddress;
	/**
	 * @ignore 
	 */
	private $_emailCCName;
	/**
	 * @ignore 
	 */
	private $_emailBCCAddress;
	/**
	 * @ignore 
	 */
	private $_emailBCCName;
	/**
	 * @ignore 
	 */
	private $_autoResponderTpl;
	/**
	 * @ignore 
	 */
	private $_autoResponderSubject;
	/**
	 * @ignore 
	 */
	private $_autoResponderToAddressField;
	/**
	 * @ignore 
	 */
	private $_autoResponderFromAddress;
	/**
	 * @ignore 
	 */
	private $_autoResponderFromName;
	/**
	 * @ignore 
	 */
	private $_autoResponderHtml;
	/**
	 * @ignore 
	 */
	private $_autoResponderReplyTo;
	/**
	 * @ignore 
	 */
	private $_autoResponderReplyToName;
	/**
	 * @ignore 
	 */
	private $_autoResponderCC;
	/**
	 * @ignore 
	 */
	private $_autoResponderCCName;
	/**
	 * @ignore 
	 */
	private $_autoResponderBCC;
	/**
	 * @ignore 
	 */
	private $_autoResponderBCCName;
	/**
	 * @ignore 
	 */
	private $_autoResponderEmailContent;
	/**
	 * @ignore 
	 */
	private $_submitVar;

	/**
	 * FormItBuilder
	 * 
	 * The main construction for FormItBuilder. All elements and rules are attached to this object.
	 * @param modx &$modx Reference to the core modX object
	 * @param string $id Id of the form
	 */
	function __construct(&$modx, $id) {
	    $this->modx = &$modx;

		$this->_method = 'post'; 
		$this->_id = $id;
		$this->_store = true;
		$this->_autoResponderHtml=true;
		$this->_formElements=array();
		$this->_rules=array();
		$this->_redirectDocument=$this->modx->resource->get('id');
		$this->_redirectParams=NULL;
		$this->_submitVar=NULL;
		$this->_jqueryValidation=false;
		$this->_emailTpl='FormItBuilderEmailTpl';
		$this->_autoResponderTpl='FormItBuilderAutoResponderEmailTpl';
		$this->_autoResponderEmailContent='<p>Thank you for your submission. A copy of the information you sent is shown below.</p>{{tableContent}}';
		
		$this->_emailFontSize='13px';
		$this->_emailFontFamily='Helvetica,Arial,sans-serif';
		$this->_emailFootHtml='<p>You can use this link to reply: <a href="mailto:{{fromEmailAddress}}?subject=RE: {{subject}}">{{fromEmailAddress}}</a></p>';
		
		//test that required snippets are available
		$snippet_formIt = $this->modx->getObject('modSnippet',array('name'=>'FormIt'));
		if($snippet_formIt===NULL){
			FormItBuilder::throwError('FormIt snippet does not appear to be installed. Please install FormIt package.');
		}
	}
	/**
	 * addRule(FormRule $formRule)
	 * 
	 * Adds a single rule to the FormItBuilder object.
	 * @param FormRule $formRule 
	 */
	public function addRule(FormRule $formRule){
		if(is_a($formRule,'FormRule')===false){
			FormItBuilder::throwError('Form rule "'.$formRule.'" is not a valid FormRule type. Recommend using FormRuleType constants to define rule type.');
		}else{
			$this->_rules[]=$formRule;
		}
	}
	/**
	 * addRules($rules)
	 * 
	 * Adds multiple FormRule objects to the FormItBuilder object.
	 * @param array $rules 
	 */
	public function addRules($rules){
		foreach($rules as $rule){
			$this->addRule($rule);
		}
	}
	
	/**
	 * getMethod()
	 * 
	 * Returns the form method (get, post etc)
	 * @return string 
	 */
	public function getMethod() { return $this->_method; }
	/**
	 * getId()
	 * 
	 * Returns the form ID.
	 * @return string
	 */
	public function getId() { return $this->_id; } 
	/**
	 * getRedirectDocument()
	 * 
	 * Returns the forms redirectDocument setting.
	 * @return string
	 */
	public function getRedirectDocument() { return $this->_redirectDocument; } 
	
	/**
	 * getRedirectParams()
	 * 
	 * Returns the forms redirectDocument parameters.
	 * @return string
	 */
	public function getRedirectParams() { return $this->_redirectParams; }
	
	/**
	 * getSubmitVar()
	 * 
	 * Returns any custom set submitVar. Will return NULL by default as normally this is an automated post variable.
	 * @return string
	 */
	public function getSubmitVar() { return $this->_submitVar; } 
	
	
			
	/**
	 * getJqueryValidation()
	 * 
	 * Returns the forms jQuery validate setting.
	 * @return boolean
	 */
	public function getJqueryValidation() { return $this->_jqueryValidation; } 
	/**
	 * getPostHookName()
	 * 
	 * Returns the post hook snippet name (normally set to the same snippet name for the FormItBuilder form)
	 * @return string
	 */
	public function getPostHookName() { return $this->_postHookName; }
	/**
	 * getEmailFromAddress()
	 * 
	 * Returns the FROM email address used when sending email.
	 * @return string
	 */
	public function getEmailFromAddress() { return $this->_emailFromAddress; }
	/**
	 * getEmailToAddress()
	 * 
	 * Returns the TO email address used when sending email.
	 * @return string
	 */
	public function getEmailToAddress() { return $this->_emailToAddress; }
	/**
	 * getEmailSubject()
	 * 
	 * Returns the email subject used when sending email.
	 * @return string
	 */
	public function getEmailSubject() { return $this->_emailSubject; }
	/**
	 * getEmailHeadHtml()
	 * 
	 * Returns the header HTML used in the email.
	 * @return string
	 */
	public function getEmailHeadHtml() { return $this->_emailHeadHtml; }
	/**
	 * getEmailFootHtml()
	 * 
	 * Returns the footer HTML used in the email.
	 * @return string
	 */
	public function getEmailFootHtml() { return $this->_emailFootHtml; }
	/**
	 * getHooks()
	 * 
	 * Returns the list of hooks that are set in the formIT call. e.g. "spam","email","redirect" etc.
	 * @return array
	 */
	public function getHooks() { return $this->_hooks; }
	/**
	 * getEmailTpl()
	 * 
	 * Returns the email template chunks used by the FormIt call. By default this is set to use FormItBuilderEmailTpl which is an installed chunk containing a single placeholder to allow FormItBuilder to dynamically generate and create the chunk content on the fly.
	 * @return array
	 */
	public function getEmailTpl() { return $this->_emailTpl; }
	/**
	 * getValidate()
	 * 
	 * Returns the custom validation methods used (doesnt include the validation rules automatically set by rules etc).
	 * @return string 
	 */
	public function getValidate() { return $this->_validate; }
	/**
	 * getCustomValidators()
	 * 
	 * Returns any customValidator settings used by the form. (Used with the FormIT "validate" command to create custom validation. See customValidators in FormIt documentation for more information).
	 * @return string
	 */
	public function getCustomValidators() { return $this->_customValidators; }
	/**
	 * getEmailFromName()
	 * 
	 * Returns the FROM email name used when sending email.
	 * @return string
	 */
	public function getEmailFromName() { return $this->_emailFromName; }
	/**
	 * getEmailToName()
	 * 
	 * Returns the TO email name used when sending email.
	 * @return string 
	 */
	public function getEmailToName() { return $this->_emailToName; }
	/**
	 * getEmailReplyToAddress()
	 * 
	 * Returns the REPLY-TO email address used when sending email.
	 * @return string 
	 */
	public function getEmailReplyToAddress() { return $this->_emailReplyToAddress; }
	/**
	 * getEmailReplyToName()
	 * 
	 * Returns the REPLY-TO email name used when sending email.
	 * @return string 
	 */
	public function getEmailReplyToName() { return $this->_emailReplyToName; }
	/**
	 * getEmailCCAddress()
	 * 
	 * Returns the CC email address used when sending email.
	 * @return string 
	 */
	public function getEmailCCAddress() { return $this->_emailCCAddress; }
	/**
	 * getEmailCCName()
	 * 
	 * Returns the CC email name used when sending email.
	 * @return string 
	 */
	public function getEmailCCName() { return $this->_emailCCName; }
	/**
	 * getEmailBCCAddress()
	 * 
	 * Returns the BCC email address used when sending email.
	 * @return string 
	 */
	public function getEmailBCCAddress() { return $this->_emailBCCAddress; }
	/**
	 * getEmailBCCName()
	 * 
	 * Returns the BCC email name used when sending email.
	 * @return string 
	 */
	public function getEmailBCCName() { return $this->_emailBCCName; }
	/**
	 * getStore()
	 * 
	 * Returns the store option used. See the FormIt "store" option.
	 * @return boolean
	 */
	public function getStore() { return $this->_store; }
	/**
	 * getPlaceholderJavascript()
	 * 
	 * Returns javascript placeholder setting used.
	 * @return string
	 */
	public function getPlaceholderJavascript() { return $this->_placeholderJavascript; } 
	/**
	 * getAutoResponderTpl()
	 * 
	 * Returns the Auto Responder template chunk used.
	 * @return string
	 */
	public function getAutoResponderTpl() { return $this->_autoResponderTpl; }
	/**
	 * getAutoResponderSubject()
	 * 
	 * Auto Responder - Returns the Auto Responder subject used in the email.
	 * @return string
	 */
	public function getAutoResponderSubject() { return $this->_autoResponderSubject; }
	/**
	 * getAutoResponderToAddressField()
	 * 
	 * Auto Responder - Returns the Auto Responder TO email address FIELD used in the email.
	 * @return string
	 */
	public function getAutoResponderToAddressField() { return $this->_autoResponderToAddressField; }
	/**
	 * getAutoResponderFromAddress()
	 * 
	 * Auto Responder - Returns the Auto Responder FROM email address used in the email.
	 * @return string
	 */
	public function getAutoResponderFromAddress() { return $this->_autoResponderFromAddress; }
	/**
	 * getAutoResponderFromName()
	 * 
	 * Auto Responder - Returns the Auto Responder FROM email name used in the email.
	 * @return string
	 */
	public function getAutoResponderFromName() { return $this->_autoResponderFromName; }
	/**
	 * getAutoResponderHtml()
	 * 
	 * Auto Responder - Returns the Auto Responder HTML setting used in the email.
	 * @return boolean
	 */
	public function getAutoResponderHtml() { return $this->_autoResponderHtml; }
	/**
	 * getAutoResponderReplyTo()
	 * 
	 * Auto Responder - Returns the Auto Responder REPLY-TO email address used in the email.
	 * @return string
	 */
	public function getAutoResponderReplyTo() { return $this->_autoResponderReplyTo; }
	/**
	 * getAutoResponderCC()
	 * 
	 * Auto Responder - Returns the Auto Responder CC email address used in the email.
	 * @return string
	 */
	public function getAutoResponderCC() { return $this->_autoResponderCC; }
	/**
	 * getAutoResponderCCName()
	 * 
	 * Auto Responder - Returns the Auto Responder CC email name used in the email.
	 * @return string
	 */
	public function getAutoResponderCCName() { return $this->_autoResponderCCName; }
	/**
	 * getAutoResponderBCC()
	 * 
	 * Auto Responder - Returns the Auto Responder BCC email address used in the email.
	 * @return string
	 */
	public function getAutoResponderBCC() { return $this->_autoResponderBCC; }
	/**
	 * getAutoResponderBCCName()
	 * 
	 * Auto Responder - Returns the Auto Responder BCC email name used in the email.
	 * @return string
	 */
	public function getAutoResponderBCCName() { return $this->_autoResponderBCCName; }
	/**
	 * getAutoResponderEmailContent()
	 * 
	 * Auto Responder - Returns the Auto Responder email content.
	 * @return string
	 */
	public function getAutoResponderEmailContent() { return $this->_autoResponderEmailContent; }	
	/**
	 * setMethod($value)
	 * 
	 * Sets the form method (get, post etc)
	 * @param string $value 
	 */
	public function setMethod($value) { $this->_method = $value; }
	/**
	 * setRedirectDocument($value)
	 * 
	 * Sets the forms redirectDocument setting (used if the redirect hook is also set).
	 * @param string $value The resource ID of the page to redirect to post success.
	 */
	public function setRedirectDocument($value) { $this->_redirectDocument = $value; } 
	/**
	 * setSubmitVar($value)
	 * 
	 * Sets the submission variable. This
	 * @param string $value A JSON object of parameters to pass in the redirect URL. e.g. {"user":"123","success":"1"}
	 */
	public function setSubmitVar($value) { $this->_submitVar = $value; } 
	
	/**
	 * setRedirectParams($value)
	 * 
	 * If set, will not begin form processing if this POST variable is not passed. Useful to disable automatic processing. Normally this process is handled automatically.
	 * @param string $value Required post variable before form processing will occur.
	 */
	public function setRedirectParams($value) { $this->_redirectParams = $value; } 
	/**
	 * setJqueryValidation($value)
	 * 
	 * Sets the forms jQuery validate setting. When set to true extra javascript is output for "jQuery Validate" to use. If jQuery Validate is installed correctly, forms should validate with inline javascript (jQuery) as well as with FormIt validation with PHP.
	 * @param boolean $value
	 */
	public function setJqueryValidation($value) { $this->_jqueryValidation = self::forceBool($value); }
	/**
	 * setPostHookName($value)
	 * 
	 * Sets the post hook snippet name (normally set to the same snippet name for the FormItBuilder form).
	 * @param string $value
	 */
	public function setPostHookName($value) { $this->_postHookName = $value; }
	/**
	 * setEmailFromAddress($value)
	 * 
	 * Sets the FROM email address used when sending email.
	 * @param string $value
	 */
	public function setEmailFromAddress($value) { $this->_emailFromAddress = $value; }
	/**
	 * setEmailToAddress($value)
	 * 
	 * Sets the TO email address used when sending email.
	 * @param string $value
	 */
	public function setEmailToAddress($value) { $this->_emailToAddress = $value; }
	/**
	 * setEmailFromName($value)
	 * 
	 * Sets the FROM email address used when sending email.
	 * @param string $value
	 */
	public function setEmailFromName($value) { $this->_emailFromName = $value; }
	/**
	 * setEmailToName($value)
	 * 
	 * Sets the TO email name used when sending email.
	 * @param string $value
	 */
	public function setEmailToName($value) { $this->_emailToName = $value; }
	/**
	 * setEmailReplyToAddress($value)
	 * 
	 * Sets the REPLY-TO email address used when sending email.
	 * @param string $value
	 */
	public function setEmailReplyToAddress($value) { $this->_emailReplyToAddress = $value; }
	/**
	 * setEmailReplyToName($value)
	 * 
	 * Sets the REPLY-TO email name used when sending email.
	 * @param string $value
	 */
	public function setEmailReplyToName($value) { $this->_emailReplyToName = $value; }
	/**
	 * setEmailCCAddress($value)
	 * 
	 * Sets the CC email address used when sending email.
	 * @param string $value
	 */
	public function setEmailCCAddress($value) { $this->_emailCCAddress = $value; }
	/**
	 * setEmailCCName($value)
	 * 
	 * Sets the FROM email name used when sending email.
	 * @param string $value
	 */
	public function setEmailCCName($value) { $this->_emailCCName = $value; }
	/**
	 * setEmailBCCAddress($value)
	 * 
	 * Sets the BCC email address used when sending email.
	 * @param string $value
	 */
	public function setEmailBCCAddress($value) { $this->_emailBCCAddress = $value; }
	/**
	 * setEmailBCCName($value)
	 * 
	 * Sets the BCC email name used when sending email.
	 * @param string $value
	 */
	public function setEmailBCCName($value) { $this->_emailBCCName = $value; }
	/**
	 * setEmailSubject($value)
	 * 
	 * Sets the email subject used when sending email.
	 * @param string $value
	 */
	public function setEmailSubject($value) { $this->_emailSubject = $value; }
	/**
	 * setEmailHeadHtml($value)
	 * 
	 * Sets the header HTML used in the email.
	 * @param string $value
	 */
	public function setEmailHeadHtml($value) { $this->_emailHeadHtml = $value; }
	/**
	 * setEmailFootHtml($value)
	 * 
	 * Sets the footer HTML used in the email.
	 * @param string $value
	 */
	public function setEmailFootHtml($value) { $this->_emailFootHtml = $value; }
	/**
	 * setHooks($value)
	 * 
	 * Sets the list of hooks that are set in the formIT call. e.g. "spam","email","redirect" etc.
	 * @param array $value Array containing hook strings
	 */
	public function setHooks($value){$this->_hooks = self::forceArray($value);}
	/**
	 * setEmailTpl($value)
	 * 
	 * Sets the email template chunk used by the FormIt call. By default this is set to use FormItBuilderEmailTpl which is an installed chunk containing a single placeholder to allow FormItBuilder to dynamically generate and create the chunk content on the fly.
	 * @param string $value
	 */
	public function setEmailTpl($value){$this->_emailTpl = $value;}
	/**
	 * setValidate($value)
	 * 
	 * Sets the custom validation methods used (doesnt include the validation rules automatically set by rules etc).
	 * @param string $value 
	 */
	public function setValidate($value) { $this->_validate = $value; }
	/**
	 * setCustomValidators($value)
	 * 
	 * Sets the customValidator settings used by the form. (Used with the FormIT "validate" command to create custom validation. See customValidators in FormIt documentation for more information).
	 * @param string $value 
	 */
	public function setCustomValidators($value) { $this->_customValidators = $value; }
	/**
	 * setDatabaseObjectForInsert($s_objName,$a_mapping)
	 * 
	 * Sets the database table object to use to automatically insert the form information on a successful submission.
	 * 
	 * <code>
	 * //Demo Table Mapping (Allows auto entry of data into an mysql Table (xPDO object)
	 * $o_form->setDatabaseObjectForInsert('tableClass',array(
	 *	array($o_name,'name'),
	 *	array($o_address,'address'),
	 *	array($o_country,'country'),           
	 * ));
	 * </code>
	 * @param object $s_objName The table class name used by the XPDO object.
	 * @param array $a_mapping Should contain an array which maps each form object to a field key within the table.
	 */
	public function setDatabaseObjectForInsert($s_objName,$a_mapping){
		$this->_databaseTableObjectName=$s_objName;
		$this->_databaseTableFieldMapping=$a_mapping;
	}
	/**
	 * setStore($value)
	 * 
	 * Sets the store option used by FormIt. If true, will store the data in the cache for retrieval using the FormItRetriever snippet.
	 * @param boolean $value 
	 */
	public function setStore($value) { $this->_store = self::forceBool($value); }
	/**
	 * setPlaceholderJavascript($value)
	 * 
	 * Sets a placeholder to use to inject any javascript used to control the form (jQuery Validate etc). By default any javascript required for forms is output inline after the closeure of the form (as you can see when viewing the HTML source). This may not be desirable for some developers as javascript order may be an issue. For simplicity it has been set this way by default for the majority of users that want to get their form up and running with minimal fuss.
	 * 
	 * To do this add the following code to your form object.
	 * <code>
	 * $o_form->setPlaceholderJavascript('FormItBuilder_javascript_myForm');
	 * </code>
	 * The string used in this method will be your placeholder name. Simply add the placeholder code to your template like so
	 * <code>
	 * [[+FormItBuilder_javascript_myForm]]
	 * </code>
	 * @param string $value 
	 */
	public function setPlaceholderJavascript($value) { $this->_placeholderJavascript = $value; }
	
	/**
	 * setAutoResponderTpl($value)
	 * 
	 * Auto Responder - Tpl chunk for auto-response message.
	 * @param string $value 
	 */
	public function setAutoResponderTpl($value) { $this->_autoResponderTpl = $value; }
	/**
	 * setAutoResponderSubject($value)
	 * 
	 * Auto Responder - The subject of the email.
	 * @param string $value 
	 */
	public function setAutoResponderSubject($value) { $this->_autoResponderSubject = $value; }
	/**
	 * setAutoResponderToAddressField($value)
	 * 
	 * Auto Responder - The name of the form field to use as the submitters email. Defaults to "email".
	 * @param string $value 
	 */
	public function setAutoResponderToAddressField($value) { $this->_autoResponderToAddressField = $value; }
	/**
	 * setAutoResponderFromAddress($value)
	 * 
	 * Auto Responder - Optional. If set, will specify the From: address for the email. Defaults to the `emailsender` system setting.
	 * @param string $value 
	 */
	public function setAutoResponderFromAddress($value) { $this->_autoResponderFromAddress = $value; }
	/**
	 * setAutoResponderFromName($value)
	 * 
	 * Auto Responder - Optional. If set, will specify the From: name for the email.
	 * @param string $value 
	 */
	public function setAutoResponderFromName($value) { $this->_autoResponderFromName = $value; }
	/**
	 * setAutoResponderHtml($value)
	 * 
	 * Auto Responder - Optional. Whether or not the email should be in HTML-format. Defaults to true.
	 * @param boolean $value 
	 */
	public function setAutoResponderHtml($value) { $this->_autoResponderHtml = self::forceBool($value); }
	/**
	 * setAutoResponderReplyTo($value)
	 * 
	 * Auto Responder - An email to set as the reply-to.
	 * @param string $value 
	 */
	public function setAutoResponderReplyTo($value) { $this->_autoResponderReplyTo = $value; }
	/**
	 * setAutoResponderReplyToName($value)
	 * 
	 * Auto Responder - Optional. The name for the Reply-To field.
	 * @param string $value 
	 */
	public function setAutoResponderReplyToName($value) { $this->_autoResponderReplyToName = $value; }
	/**
	 * setAutoResponderCC($value)
	 * 
	 * Auto Responder - Optional. A comma-separated list of emails to send via cc.
	 * @param string $value 
	 */
	public function setAutoResponderCC($value) { $this->_autoResponderCC = $value; }
	/**
	 * setAutoResponderCCName($value)
	 * 
	 * Auto Responder - Optional. A comma-separated list of names to pair with the fiarCC values.
	 * @param string $value 
	 */
	public function setAutoResponderCCName($value) { $this->_autoResponderCCName = $value; }
	/**
	 * setAutoResponderBCC($value)
	 * 
	 * Auto Responder - Optional. A comma-separated list of emails to send via bcc.
	 * @param string $value 
	 */
	public function setAutoResponderBCC($value) { $this->_autoResponderBCC = $value; }
	/**
	 * setAutoResponderBCCName($value)
	 * 
	 * Auto Responder - Optional. A comma-separated list of names to pair with the fiarBCC values.
	 * @param string $value 
	 */
	public function setAutoResponderBCCName($value) { $this->_autoResponderBCCName = $value; }
	/**
	 * setAutoResponderEmailContent($value)
	 * 
	 * Auto Responder - Sets the email content.
	 * @param string $value 
	 */
	public function setAutoResponderEmailContent($value) { $this->_autoResponderEmailContent = $value; }
    /**
	 * addElement(FormItBuilder_baseElement $o_formElement)
	 * 
	 * Adds a single element object to the main FormItBuilder object.
	 * @param FormItBuilder_baseElement $o_formElement 
	 */
	public function addElement(FormItBuilder_baseElement $o_formElement){
		$this->_formElements[]=$o_formElement;
	}
    /**
	 * addElements($a_elements)
	 * 
	 * Adds multiple element objects to the main FormItBuilder object.
	 * @param array $a_elements An array of objects that extend FormItBuilder_baseElement. 
	 */
	public function addElements($a_elements){
		foreach($a_elements as $o_formElement){
			$this->addElement($o_formElement);
		}
	}
	/**
	 * addToDatabase($s_ObjName,$a_mapping)
	 * 
	 * Adds the form element objects content to an XPDO table.
	 * @param string $s_ObjName
	 * @param array $a_mapping
	 * @return boolean 
	 * @ignore 
	 */
	private function addToDatabase($s_ObjName,$a_mapping){
		//inspired by http://bobsguides.com/custom-db-tables.html
		$fields = array();
		foreach($a_mapping as $a){
			$o_formObj = $a[0];
			$s_keyName = $a[1];
			
			$fields[$s_keyName]=$_POST[$o_formObj->getId()];
		};
		$newObj = $this->modx->newObject($s_ObjName, $fields);
		$res = $newObj->save();
		if($res===true){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * processHooks($a_hookCommands)
	 * 
	 * Called from the FormItBuilder_hooks snippet. Not intended to be called publically in any other way. This process should be automatic.
	 * @param array $a_hookCommands
	 * @return boolean
	 */
	public function processHooks($a_hookCommands){
		$i_okCount=0;
		foreach($a_hookCommands as $a_cmd){
			$b_res=false;
			if(isset($a_cmd['name'],$a_cmd['value'])===true){
				switch($a_cmd['name']){
					case 'dbEntry':
						if(isset($a_cmd['value']['tableObj'],$a_cmd['value']['mapping'])===true){
							$b_res = $this->addToDatabase($a_cmd['value']['tableObj'],$a_cmd['value']['mapping']);
						}else{
							FormItBuilder::throwError('FormItBuilder processHooks failed. The tableObj or mapping attributes were not set for "'.$a_cmd['name'].'".');		
						}
						break;
				}
				if($b_res===true){
					$i_okCount++;
				}
			}else{
				FormItBuilder::throwError('FormItBuilder processHooks failed. The name and value pair is not set.');
			}
		}
		if($i_okCount==count($a_hookCommands)){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * getFormTableContent()
	 * 
	 * Gets the form value TABLE HTML content.
	 * @return string 
	 * @ignore 
	 */
	private function getFormTableContent(){
		$s_style = 'font-size:'.$this->_emailFontSize.'; font-family:'.$this->_emailFontFamily.';';
		$bgCol1='#FFFFFF';
		$bgCol2='#e4edf9';
		$bgColDarkerBG='#cddaeb';
		$colOutline='#8a99ae';
		$rowCount=0;
		$s_ret='<table cellpadding="5" cellspacing="0" style="'.$s_style.'">'.$NL;
		foreach($this->_formElements as $o_el){
			if(get_class($o_el)=='FormItBuilder_htmlBlock'){
				//do nothing
			}else{
				if($o_el->showInEmail()===true){
					
					$bgCol=$bgCol1;
					if($rowCount%2==0){
						$bgCol=$bgCol2;
					}

					$elType=get_class($o_el);
					$elId = $o_el->getId();
					
					switch($elType){
						case 'FormItBuilder_elementMatrix':
							$type = $o_el->getType();
							$cols = $o_el->getColumns();
							$rows = $o_el->getRows();
							$r_cnt=0;
							$s_val='<table cellpadding="5" cellspacing="0" style="'.$s_style.' font-size:10px;"><tr><td>&nbsp;</td>';
							$c_cnt=0;
							foreach($cols as $column){
								$s_val.='<td style="'.($c_cnt==0?'border-left:1px solid '.$colOutline.'; ':'').'background-color:'.$bgColDarkerBG.'; border-right:1px solid '.$colOutline.'; border-bottom:1px solid '.$colOutline.'; border-top:1px solid '.$colOutline.';"><em>'.htmlspecialchars($column).'</em></td>';
								$c_cnt++;								
							}
							$s_val.='</tr>';
							foreach($rows as $row){
								$c_cnt=0;
								$s_val.='<tr><td style="'.($r_cnt==0?'border-top:1px solid '.$colOutline.'; ':'').'background-color:'.$bgColDarkerBG.'; border-right:1px solid '.$colOutline.'; border-left:1px solid '.$colOutline.'; border-bottom:1px solid '.$colOutline.';"><em>'.htmlspecialchars($row).'</em></td>';
								foreach($cols as $column){
									$s_val.='<td style="text-align:center; border-right:1px solid '.$colOutline.'; border-bottom:1px solid '.$colOutline.';">';
									switch($type){
										case 'text':
											$s_val.=htmlspecialchars($_REQUEST[$elId.'_'.$r_cnt.'_'.$c_cnt]);
											break;
										case 'radio':
											$s_val.=($c_cnt==$_REQUEST[$elId.'_'.$r_cnt]?'&#10004;':'-');
											break;
										case 'check':
											if(isset($_REQUEST[$elId.'_'.$r_cnt]) && in_array($c_cnt,$_REQUEST[$elId.'_'.$r_cnt])===true){
												$s_val.='&#10004;';
											}else{
												$s_val.='-';
											}
											break;
									}
									$s_val.='</td>';
									$c_cnt++;
								}
								$r_cnt++;
								$s_val.='</tr>';
							}
							$s_val.='</table>';
							break;
						case 'FormItBuilder_elementFile':
							if(isset($_FILES[$elId])){
								if($_FILES[$elId]['size']==0){
									$s_val='None';
								}
							}
							break;
						case 'FormItBuilder_elementDate':
							$s_val='[[+'.htmlspecialchars($o_el->getId()).'_0]] [[+'.htmlspecialchars($o_el->getId()).'_1]] [[+'.htmlspecialchars($o_el->getId()).'_2]]';
							break;							
						default:
							$s_val='[[+'.htmlspecialchars($o_el->getId()).':nl2br]]';
							break;
					}
					
					$s_ret.='<tr valign="top" bgcolor="'.$bgCol.'"><td><b>'.htmlspecialchars($o_el->getLabel()).':</b></td><td>'.$s_val.'</td></tr>'.$NL;
					$rowCount++;
				}
			}
		}
		$s_ret.='</table>'.$NL;
		return $s_ret;
	}
	/**
	 * autoResponderEmailStr()
	 * 
	 * Gets the Auto Responder email content.
	 * @return string 
	 * @ignore 
	 */
	private function autoResponderEmailStr(){
		$NL="\r\n";
		$s_style = 'font-size:'.$this->_emailFontSize.'; font-family:'.$this->_emailFontFamily.';';
		
		$s_emailContent = $this->_autoResponderEmailContent;
		$s_emailContent = str_replace('{{tableContent}}', $this->getFormTableContent(), $s_emailContent);
		
		$s_ret='<div style="'.$s_style.'">'.$NL.$s_emailContent.$NL.'</div>';
		return $s_ret;
	}
	/**
	 * getPostHookString()
	 * 
	 * Gets the strink used for the post hook (email content).
	 * @return string 
	 * @ignore 
	 */
	
	function getPostHookString(){
		$NL="\r\n";
		$s_style = 'font-size:'.$this->_emailFontSize.'; font-family:'.$this->_emailFontFamily.';';
		
		$s_footHTML = $this->_emailFootHtml;
		$s_footHTML	= str_replace(
			array('{{fromEmailAddress}}','{{subject}}'),
			array(htmlspecialchars($this->_emailFromAddress),htmlspecialchars($this->_emailSubject)),
			$s_footHTML
		);

		$s_ret='<div style="'.$s_style.'">'.$NL.$this->_emailHeadHtml.$NL
		.$this->getFormTableContent().$NL
		.$s_footHTML.$NL
		.'</div>';
		
		return $s_ret;
	}
	/**
	 * processCoreHook(&$hook,&$formObj)
	 * 
	 * Other hooks call this method which sets placeholder content for email and autoresponder template chunks.
	 * @param hook $hook Reference to the formIt hook object.
	 * @param FormItBuilder $formObj Reference to the FormItBuilder form object.
	 * @return boolean 
	 */
	public function processCoreHook(&$hook,&$formObj){
		$hook->setValue('FormItBuilderEmailTpl',$formObj->postHook());
		//$this->modx->setPlaceholder('FormItBuilderAutoResponderEmailTpl','asfsafa');
		$hook->setValue('FormItBuilderAutoResponderEmailTpl',$formObj->autoResponderEmailStr());
		return true;
	}
	/**
	 * postHook() - SOON TO BE MADE PRIVATE - SHOULD CALL processCoreHook METHOD INSTEAD
	 * 
	 * Gets the post hook email template chunk string.
	 * @return string 
	 */
	public function postHook(){
		return $this->getPostHookString();
	}
	/**
	 * postHookRaw()
	 * 
	 * FOR DEBUGGING ONLY - Gets the post hook string and echos it out followed by a hard exit. This allows users to see the raw FormIt syntax to ensure the source is written as expected.
	 */
	public function postHookRaw(){
		echo $this->getPostHookString();
		exit();
	}
	/**
	 * jqueryValidateJSON($jqFieldProps,$jqFieldMessages,$jqFormRules,$jqFormMessages)
	 * 
	 * Processes the fields into jQueryValidate output
	 * @param string $jqFieldProps
	 * @param string $jqFieldMessages
	 * @param string $jqFormRules
	 * @param string $jqFormMessages
	 * @return string 
	 * @ignore 
	 */
	private function jqueryValidateJSON($jqFieldProps,$jqFieldMessages,$jqGroups){
		$a_ruleSegs = array();
		$a_msgSegs = array();
		foreach($jqFieldProps as $fieldID=>$a_fieldProp){
			if(count($a_fieldProp)>0){
				$a_ruleSegs[]='\''.$fieldID.'\':{'.implode(',',$a_fieldProp).'}';
			}
		}
		foreach($jqFieldMessages as $fieldID=>$a_fieldMsg){
			if(count($a_fieldMsg)>0){
				$a_msgSegs[]='\''.$fieldID.'\':{'.implode(',',$a_fieldMsg).'}';
			}
		}
		$s_js=
		'rules:{  '."\r\n  ".implode(",\r\n  ",$a_ruleSegs)."\r\n".'},'.
		'messages:{  '."\r\n  ".implode(",\r\n  ",$a_msgSegs)."\r\n".'}'
		;
		if(empty($jqGroups)===false){
			$s_js.=',groups:{'."\r\n";
			$a_groupLines=array();
			foreach($jqGroups as $key=>$val){
				$a_groupLines[]=$key.':"'.$val.'"';
			}
			$s_js.=implode(",\r\n",$a_groupLines);
			$s_js.='}'."\r\n";
		}
		
		return $s_js;
	}
	/**
	 * getFormItBuilderOutput()
	 * 
	 * Constructs the FormItBuilder source. This is the main processing function.
	 * @return string
	 * @ignore 
	 */
	private function getFormItBuilderOutput(){
		$s_submitVar = 'submitVar_'.$this->_id;
		$b_customSubmitVar=false;
		if(empty($this->_submitVar)===false){
			$s_submitVar = $this->_submitVar;
			$b_customSubmitVar=true;
		}
		$s_recaptchaJS='';
		$b_posted = false;
		if(isset($_REQUEST[$s_submitVar])===true){
			$b_posted=true;
		}
		$nl="\r\n";

		//process and add form rules
		$a_fieldProps=array();
		$a_fieldProps_jqValidate=array();
		$a_fieldProps_jqValidateGroups=array();
		$a_fieldProps_errstringFormIt=array();
		$a_fieldProps_errstringJq=array();
		
		$a_formProps=array();
		$a_formProps_custValidate=array();
		$a_formPropsFormItErrorStrings=array();

		foreach($this->_rules as $rule){
			$o_elFull = $rule->getElement();
			if(is_array($o_elFull)===true){
				$o_el = $o_elFull[0];
			}else{
				$o_el = $o_elFull;
			}
			$elId = $o_el->getId();
			$elName = $o_el->getName();
			if(isset($a_fieldProps[$elId])===false){
				$a_fieldProps[$elId]=array();
			}
			if(isset($a_fieldProps[$elId])===false){
				$a_fieldProps[$elId]=array();
			}
			if(isset($a_fieldProps_jqValidate[$elId])===false){
				$a_fieldProps_jqValidate[$elId]=array();
			}
			if(isset($a_fieldProps_errstringFormIt[$elId])===false){
				$a_fieldProps_errstringFormIt[$elId]=array();
			}
			if(isset($a_fieldProps_errstringJq[$elId])===false){
				$a_fieldProps_errstringJq[$elId]=array();
			}
			if(isset($a_formProps_custValidate[$elId])===false){
				$a_formProps_custValidate[$elId]=array();
			}
			
			$s_validationMessage=$rule->getValidationMessage();
			switch($rule->getType()){
				case FormRuleType::email:
					$a_fieldProps[$elId][] = 'email';
					$a_fieldProps_errstringFormIt[$elId][] = 'vTextEmailInvalid=`'.$s_validationMessage.'`';
					$a_fieldProps_jqValidate[$elName][] = 'email:true';
					$a_fieldProps_errstringJq[$elName][] = 'email:"'.$s_validationMessage.'"';
					break;
				case FormRuleType::fieldMatch:
					$a_fieldProps[$elId][] = 'password_confirm=^'.$o_elFull[1]->getId().'^';
					$a_fieldProps_errstringFormIt[$elId][] = 'vTextPasswordConfirm=`'.$s_validationMessage.'`';
					$a_fieldProps_jqValidate[$elName][] = 'equalTo:"#'.$o_elFull[1]->getId().'"';
					$a_fieldProps_errstringJq[$elName][] = 'equalTo:"'.$s_validationMessage.'"';
					break;
				case FormRuleType::maximumLength:
					if(is_a($o_el, 'FormItBuilder_elementCheckboxGroup')){
						$a_formProps_custValidate[$elId][]=array('type'=>'checkboxGroup','maxLength'=>$rule->getValue(),'errorMessage'=>$s_validationMessage);
						$a_fieldProps[$elId][] = 'FormItBuilder_customValidation';
					}else{
						$a_fieldProps[$elId][] = 'maxLength=^'.$rule->getValue().'^';
						$a_fieldProps_errstringFormIt[$elId][] = 'vTextMaxLength=`'.$s_validationMessage.'`';
					}
					$a_fieldProps_jqValidate[$elName][] = 'maxlength:'.$rule->getValue();
					$a_fieldProps_errstringJq[$elName][] = 'maxlength:"'.$s_validationMessage.'"';
					break;
				case FormRuleType::maximumValue:
					$a_fieldProps[$elId][] = 'maxValue=^'.$rule->getValue().'^';
					$a_fieldProps_errstringFormIt[$elId][] = 'vTextMaxValue=`'.$s_validationMessage.'`';
					$a_fieldProps_jqValidate[$elName][] = 'max:'.$rule->getValue();
					$a_fieldProps_errstringJq[$elName][] = 'max:"'.$s_validationMessage.'"';
					break;
				case FormRuleType::minimumLength:
					if(is_a($o_el, 'FormItBuilder_elementCheckboxGroup')){
						$a_formProps_custValidate[$elId][]=array('type'=>'checkboxGroup','minLength'=>$rule->getValue(),'errorMessage'=>$s_validationMessage);
						$a_fieldProps[$elId][] = 'FormItBuilder_customValidation';
					}else{
						$a_formProps_custValidate[$elId][]=array('type'=>'textfield','minLength'=>$rule->getValue(),'errorMessage'=>$s_validationMessage);
						$a_fieldProps[$elId][] = 'FormItBuilder_customValidation';
					}
					//Made own validation rule cause FormIt doesnt behave with required.
					//$a_fieldProps_errstringFormIt[$elId][] = 'vTextMinLength=`'.$s_validationMessage.'`';
					$a_fieldProps_jqValidate[$elName][] = 'minlength:'.$rule->getValue();
					$a_fieldProps_errstringJq[$elName][] = 'minlength:"'.$s_validationMessage.'"';
					break;
				case FormRuleType::minimumValue:
					$a_fieldProps[$elId][] = 'minValue=^'.$rule->getValue().'^';
					$a_fieldProps_errstringFormIt[$elId][] = 'vTextMinValue=`'.$s_validationMessage.'`';
					$a_fieldProps_jqValidate[$elName][] = 'min:'.$rule->getValue();
					$a_fieldProps_errstringJq[$elName][] = 'min:"'.$s_validationMessage.'"';
					break;
				case FormRuleType::numeric:
					$a_fieldProps[$elId][] = 'isNumber';
					$a_fieldProps_errstringFormIt[$elId][] = 'vTextIsNumber=`'.$s_validationMessage.'`';
					$a_fieldProps_jqValidate[$elName][] = 'digits:true';
					$a_fieldProps_errstringJq[$elName][] = 'digits:"'.$s_validationMessage.'"';
					break;
				case FormRuleType::required:
					if(is_a($o_el, 'FormItBuilder_elementMatrix')){
						$s_type=$o_el->getType();
						$a_formProps_custValidate[$elId][]=array('type'=>'elementMatrix_'.$s_type,'required'=>true,'errorMessage'=>$s_validationMessage);
						$a_fieldProps[$elId][] = 'FormItBuilder_customValidation';
						$a_rows = $o_el->getRows();
						$a_columns = $o_el->getColumns();
						$a_namesForGroup=array();
						switch($s_type){
							case 'text':
								for($row_cnt=0; $row_cnt<count($a_rows);$row_cnt++){
									for($col_cnt=0; $col_cnt<count($a_columns); $col_cnt++){
										$a_namesForGroup[]=$elName.'_'.$row_cnt.'_'.$col_cnt;
										$a_fieldProps_jqValidate[$elName.'_'.$row_cnt.'_'.$col_cnt][] = 'required:true';
										$a_fieldProps_errstringJq[$elName.'_'.$row_cnt.'_'.$col_cnt][] = 'required:"'.$s_validationMessage.'"';
									}	
								}
								break;
							case 'radio':
								for($row_cnt=0; $row_cnt<count($a_rows);$row_cnt++){
									$a_namesForGroup[]=$elName.'_'.$row_cnt;
									$a_fieldProps_jqValidate[$elName.'_'.$row_cnt][] = 'required:true';
									$a_fieldProps_errstringJq[$elName.'_'.$row_cnt][] = 'required:"'.$s_validationMessage.'"';
								}
								break;
							case 'check':
								for($row_cnt=0; $row_cnt<count($a_rows);$row_cnt++){
									$s_fieldName = $elName.'_'.$row_cnt.'[]';
									$a_namesForGroup[]=$s_fieldName;
									$a_fieldProps_jqValidate[$s_fieldName][] = 'required:true';
									$a_fieldProps_errstringJq[$s_fieldName][] = 'required:"'.$s_validationMessage.'"';
								}
								break;
						}
						$a_fieldProps_jqValidateGroups[$elName]=implode(' ',$a_namesForGroup);
					}else if(is_a($o_el, 'FormItBuilder_elementDate')){
						$a_formProps_custValidate[$elId][]=array('type'=>'elementDate','required'=>true,'errorMessage'=>$s_validationMessage);
						$a_fieldProps[$elId][] = 'FormItBuilder_customValidation';
						$a_fieldProps_jqValidate[$elName.'_0'][] = 'required:true,dateElementRequired:true';
						$a_fieldProps_errstringJq[$elName.'_0'][] = 'required:"'.$s_validationMessage.'",dateElementRequired:"'.$s_validationMessage.'"';
					}else{
						$a_formProps_custValidate[$elId][]=array('type'=>'textfield','required'=>true,'errorMessage'=>$s_validationMessage);
						$a_fieldProps[$elId][] = 'FormItBuilder_customValidation';
						$a_fieldProps_jqValidate[$elName][] = 'required:true';
						$a_fieldProps_errstringJq[$elName][] = 'required:"'.$s_validationMessage.'"';
					}
					break;
				case FormRuleType::date:
					$s_thisVal = $rule->getValue();
					$s_thisErrorMsg = str_replace('===dateformat===',$s_thisVal,$s_validationMessage);
					$a_formProps_custValidate[$elId][]=array('type'=>'date','fieldFormat'=>$s_thisVal,'errorMessage'=>$s_thisErrorMsg);
					$a_fieldProps[$elId][] = 'FormItBuilder_customValidation';
					$a_fieldProps_jqValidate[$elName][] = 'dateFormat:\''.$s_thisVal.'\'';
					$a_fieldProps_errstringJq[$elName][] = 'dateFormat:"'.$s_thisErrorMsg.'"';
					break;
			}
		}
		//if some custom validation options were found (date etc) then add formItBuilder custom validate snippet to the list
		if(count($a_formProps_custValidate)>0){
			$GLOBALS['FormItBuilder_customValidation']=$a_formProps_custValidate;
			if(empty($this->_customValidators)===false){
				$this->_customValidators.=',';
			}
			$this->_customValidators.='FormItBuilder_customValidation';
		}
		
		//build inner form html
		$b_attachmentIncluded=false;
		$s_form='<div>'.$nl
		.$nl.'<div class="process_errors_wrap"><div class="process_errors">[[!+fi.error_message:notempty=`[[!+fi.error_message]]`]]</div></div>'
		.$nl.($b_customSubmitVar===false?'<input type="hidden" name="'.$s_submitVar.'" value="1" />':'')
		.$nl.'<input type="hidden" name="fke'.date('Y').'Sp'.date('m').'Blk:blank" value="" /><!-- additional crude spam block. If this field ends up with data it will fail to submit -->'
		.$nl;
		foreach($this->_formElements as $o_el){
			$s_elClass=get_class($o_el);
			if($s_elClass=='FormItBuilder_elementFile'){
				$b_attachmentIncluded=true;
			}
			if(is_a($o_el,'FormItBuilder_elementHidden')){
				$s_form.=$o_el->outputHTML();
			}else if(is_a($o_el,'FormItBuilder_htmlBlock')){
				$s_form.=$o_el->outputHTML();
			}else{
				$s_typeClass = substr($s_elClass,14,strlen($s_elClass)-14);
				$forId=$o_el->getId();
				if(
					is_a($o_el,'FormItBuilder_elementRadioGroup')===true
					|| is_a($o_el,'FormItBuilder_elementCheckboxGroup')===true
					|| is_a($o_el,'FormItBuilder_elementDate')===true
				){
					$forId=$o_el->getId().'_0';
				}
				$s_forStr = ' for="'.htmlspecialchars($forId).'"';
				
				if(is_a($o_el,'FormItBuilder_elementReCaptcha')===true){
					$s_forStr = ''; // dont use for attrib for Recaptcha (as it is an external program outside control of formitbuilder
					$s_recaptchaJS=$o_el->getJsonConfig();
				}
				
				$s_extraClasses='';
				$a_exClasses=$o_el->getExtraClasses();
				if(count($a_exClasses)>0){
					$s_extraClasses = ' '.implode(' ',$o_el->getExtraClasses());
				}
				
				$b_required = $o_el->isRequired();
				$s_form.='<div title="'.$o_el->getLabel().'" class="formSegWrap formSegWrap_'.htmlspecialchars($o_el->getId()).' '.$s_typeClass.($b_required===true?' required':'').$s_extraClasses.'">';
					$s_labelHTML='';
					if($o_el->showLabel()===true){
						$s_desc=$o_el->getDescription();
						if(empty($s_desc)===false){
							$s_desc='<span class="description">'.$s_desc.'</span>';
						}
						$s_labelHTML='<label class="mainElLabel"'.$s_forStr.'><span class="before"></span><span class="mainLabel">'.$o_el->getLabel().'</span>'.$s_desc.'<span class="after"></span></label>';
					}
					
					$s_element='<div class="elWrap">'.$nl.'    <span class="before"></span>'.$o_el->outputHTML().'<span class="after"></span>';
					if($o_el->showLabel()===true){
						$s_element.='<div class="errorContainer"><label class="formiterror" '.$s_forStr.'>[[+fi.error.'.htmlspecialchars($o_el->getId()).']]</label></div>';
					}
					$s_element.='</div>';
					
					if($o_el->getLabelAfterElement()===true){
						$s_form.=$s_element.$s_labelHTML;
					}else{
						$s_form.=$s_labelHTML.$s_element;
					}
				$s_form.=$nl.'</div>'.$nl;
			}
		}
		$s_form.=$nl.'</div>';

		//wrap form elements in form tags
		$s_form='<form action="[[~[[*id]]]]" method="'.htmlspecialchars($this->_method).'"'.($b_attachmentIncluded?' enctype="multipart/form-data"':'').' class="form" id="'.htmlspecialchars($this->_id).'">'.$nl
		.$s_form.$nl
		.'</form>';
		
		//add all formit validation rules together in one array for easy implode
		$a_formItCmds=array();
		$a_formItErrorMessage=array();
		foreach($a_fieldProps as $fieldID=>$a_fieldProp){
			if(count($a_fieldProp)>0){
				$a_formItCmds[]=$fieldID.':'.implode(':',$a_fieldProp);
			}
		}
		//add formIT error messages
		foreach($a_fieldProps_errstringFormIt as $fieldID=>$msgArray){
			foreach($msgArray as $msg){
				$a_formItErrorMessage[]='&'.$fieldID.'.'.$msg;
			}
		}
		
		for($i=0; $i<count($a_formProps); $i++){
			$a_formItCmds[]=$a_formProps[$i];
			if(empty($a_formPropsFormItErrorStrings[$i])===false){
				$a_formItErrorMessage[]=$a_formPropsFormItErrorStrings[$i];
			}
		}
		
		//if using database table then add call to final hook
		$b_addFinalHooks=false;
		$GLOBALS['FormItBuilder_hookCommands']=array('formObj'=>&$this,'commands'=>array());
		if(empty($this->_databaseTableObjectName)===false){
			$GLOBALS['FormItBuilder_hookCommands']['commands'][]=array('name'=>'dbEntry','value'=>array('tableObj'=>$this->_databaseTableObjectName,'mapping'=>$this->_databaseTableFieldMapping));
			$b_addFinalHooks=true;
		}
		if($b_addFinalHooks===true){
			$this->_hooks[]='FormItBuilder_hooks';
		}
		
		
		$s_formItCmd='[[!FormIt?'
		.$nl.'&hooks=`'.$this->_postHookName.(count($this->_hooks)>0?','.implode(',',$this->_hooks):'').'`'
				
		.(empty($s_recaptchaJS)===false?$nl.'&recaptchaJs=`'.$s_recaptchaJS.'`':'')
		.(empty($this->_customValidators)===false?$nl.'&customValidators=`'.$this->_customValidators.'`':'')
			
		.(empty($this->_emailTpl)===false?$nl.'&emailTpl=`'.$this->_emailTpl.'`':'')
		.(empty($this->_emailToAddress)===false?$nl.'&emailTo=`'.$this->_emailToAddress.'`':'')
		.(empty($this->_emailToName)===false?$nl.'&emailToName=`'.$this->_emailToName.'`':'')
		.(empty($this->_emailFromAddress)===false?$nl.'&emailFrom=`'.$this->_emailFromAddress.'`':'')
		.(empty($this->_emailFromName)===false?$nl.'&emailFromName=`'.$this->_emailFromName.'`':'')
		.(empty($this->_emailReplyToAddress)===false?$nl.'&emailReplyTo=`'.$this->_emailReplyToAddress.'`':'')
		.(empty($this->_emailReplyToName)===false?$nl.'&emailReplyToName=`'.$this->_emailReplyToName.'`':'')
		.(empty($this->_emailCCAddress)===false?$nl.'&emailCC=`'.$this->_emailCCAddress.'`':'')
		.(empty($this->_emailCCName)===false?$nl.'&emailCCName=`'.$this->_emailCCName.'`':'')
		.(empty($this->_emailBCCAddress)===false?$nl.'&emailBCC=`'.$this->_emailBCCAddress.'`':'')
		.(empty($this->_emailBCCName)===false?$nl.'&emailBCCName=`'.$this->_emailBCCName.'`':'')
		
		.(empty($this->_autoResponderTpl)===false?$nl.'&fiarTpl=`'.$this->_autoResponderTpl.'`':'')
		.(empty($this->_autoResponderSubject)===false?$nl.'&fiarSubject=`'.$this->_autoResponderSubject.'`':'')
		.(empty($this->_autoResponderToAddressField)===false?$nl.'&fiarToField=`'.$this->_autoResponderToAddressField.'`':'')
		.(empty($this->_autoResponderFromAddress)===false?$nl.'&fiarFrom=`'.$this->_autoResponderFromAddress.'`':'')
		.(empty($this->_autoResponderFromName)===false?$nl.'&fiarFromName=`'.$this->_autoResponderFromName.'`':'')
		.(empty($this->_autoResponderReplyTo)===false?$nl.'&fiarReplyTo=`'.$this->_autoResponderReplyTo.'`':'')
		.(empty($this->_autoResponderReplyToName)===false?$nl.'&fiarReplyToName=`'.$this->_autoResponderReplyToName.'`':'')
		.(empty($this->_autoResponderCC)===false?$nl.'&fiarCC=`'.$this->_autoResponderCC.'`':'')
		.(empty($this->_autoResponderCCName)===false?$nl.'&fiarCCName=`'.$this->_autoResponderCCName.'`':'')
		.(empty($this->_autoResponderBCC)===false?$nl.'&fiarBCC=`'.$this->_autoResponderBCC.'`':'')
		.(empty($this->_autoResponderBCCName)===false?$nl.'&fiarBCCName=`'.$this->_autoResponderBCCName.'`':'')
		.$nl.'&fiarHtml=`'.($this->_autoResponderHtml===false?'0':'1').'`'				
				
		.$nl.'&emailSubject=`'.$this->_emailSubject.'`'
		.$nl.'&emailUseFieldForSubject=`1`'
		.$nl.'&redirectTo=`'.$this->_redirectDocument.'`'
		.(empty($this->_redirectParams)===false?$nl.'&redirectParams=`'.$this->_redirectParams.'`':'')
		.$nl.'&store=`'.($this->_store===true?'1':'0').'`'
		.$nl.'&submitVar=`'.$s_submitVar.'`'
		.$nl.implode($nl,$a_formItErrorMessage)
		.$nl.'&validate=`'.(isset($this->_validate)?$this->_validate.',':'').implode(','.$nl.' ',$a_formItCmds).','.$nl.'`]]'.$nl;
		
		if($this->_jqueryValidation===true){
			$s_js='	
$().ready(function() {

jQuery.validator.addMethod("dateFormat", function(value, element, format) {
	var b_retStatus=false;
	var s_retValue="";
	var n_retTimestamp=0;
	if(value.length==format.length){
		var separator_only = format;
		var testDate;
		if(format.toLowerCase()=="yyyy"){
			//allow just yyyy
			testDate = new Date(value, 1, 1);
			if(testDate.getFullYear()==value){
				b_retStatus=true;
			}
		}else{
			separator_only = separator_only.replace(/m|d|y/g,"");
			var separator = separator_only.charAt(0)

			if(separator && separator_only.length==2){
				var dayPos; var day; var monthPos; var month; var yearPos; var year;
				var s_testYear;
				var newStr = format;

				dayPos = format.indexOf("dd");
				day = parseInt(value.substr(dayPos,2),10)+"";
				if(day.length==1){day="0"+day;}
				newStr=newStr.replace("dd",day);

				monthPos = format.indexOf("mm");
				month = parseInt(value.substr(monthPos,2),10)+"";
				if(month.length==1){month="0"+month;}
				newStr=newStr.replace("mm",month);

				yearPos = format.indexOf("yyyy");
				year = parseInt(value.substr(yearPos,4),10);
				newStr=newStr.replace("yyyy",year);

				testDate = new Date(year, month-1, day);

				var testDateDay=(testDate.getDate())+"";
				if(testDateDay.length==1){testDateDay="0"+testDateDay;}

				var testDateMonth=(testDate.getMonth()+1)+"";
				if(testDateMonth.length==1){testDateMonth="0"+testDateMonth;}

				if (testDateDay==day && testDateMonth==month && testDate.getFullYear()==year) {
					b_retStatus = true;
					$(element).val(newStr);
				}
			}
		}
	}
	return this.optional(element) || b_retStatus;
}, "Please enter a valid date.");

jQuery.validator.addMethod("dateElementRequired", function(value, element, format) {
	var el=element;
	b_retStatus=true;
	var elBaseId=element.id.substr(0,element.id.length-2);
	if($("#"+elBaseId+"_0").val()=="" || $("#"+elBaseId+"_1").val()=="" || $("#"+elBaseId+"_2").val()==""){
		b_retStatus=false;
	}
	return this.optional(element) || b_retStatus;
}, "Date element is required.");

//Main validate call
var thisFormEl=$("#'.$this->_id.'");
thisFormEl.validate({errorPlacement:function(error, element) {
	var labelEl = element.parents(".formSegWrap").find(".errorContainer");
	error.appendTo( labelEl );
},success: function(element) {
	element.addClass("valid");
	var formSegWrapEl = element.parents(".formSegWrap");
	formSegWrapEl.children(".mainElLabel").removeClass("mainLabelError");
},highlight: function(el, errorClass, validClass) {
	var element= $(el);
	element.addClass(errorClass).removeClass(validClass);
	element.parents(".formSegWrap").children(".mainElLabel").addClass("mainLabelError");
},invalidHandler: function(form, validator){
	//make nice little animation to scroll to the first invalid element instead of an instant jump
	var jumpEl = $("#"+validator.errorList[0].element.id).parents(".formSegWrap");
	$("html,body").animate({scrollTop: jumpEl.offset().top});
},ignore:":hidden",'.
					
$this->jqueryValidateJSON(
	$a_fieldProps_jqValidate,
	$a_fieldProps_errstringJq,
	$a_fieldProps_jqValidateGroups
).'});
	
'.
//Force validation on load if already posted
($b_posted===true?'thisFormEl.valid();':'')
.'
	
});
';
		}
		
		//Allows output of the javascript into a paceholder so in can be inserted elsewhere in html (head etc)
		if(empty($this->_placeholderJavascript)===false){
			$this->modx->setPlaceholder($this->_placeholderJavascript,$s_js);
			return $s_formItCmd.$s_form;
		}else{
			return $s_formItCmd.$s_form.
'<script type="text/javascript">
// <![CDATA[
'.$s_js.'
// ]]>
</script>';
		}
	}
	/**
	 * output()
	 * 
	 * Output the FormItBuilder source.
	 * @return string
	 */
	public function output(){
		return $this->getFormItBuilderOutput();
	}
	/**
	 * outputRaw()
	 * 
	 * FOR DEBUGGING ONLY - Gets the FormItBuilder output source and prints to screen. Following this a hard exit is called. This allows users to see the raw FormIt syntax to ensure the source is written as expected.
	 */
	public function outputRaw(){
		echo $this->getFormItBuilderOutput();
		exit();
	}
}
?>