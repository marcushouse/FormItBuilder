<?php
/**
 * @package FormItBuilder
 */

/**
 * Required Files
 */
require_once 'FormRuleType.class.php';

/**
 * Contains rule methods applied to an element. The FormItBuilder object is then assigned rules using the addRules method.
 * @package FormItBuilder
 */
class FormRule extends FormItBuilderCore{
	private $_type;
	private $_element;
	private $_value;
	private $_validationMessage;
	
	public function getType() { return $this->_type; }
	public function getElement() { return $this->_element; }
	public function getValue() { return $this->_value; }
	public function getValidationMessage() { return $this->_validationMessage; }
	
	public function setValidationMessage($value) { $this->_validationMessage = $value; }
	
	/**
	 * Form Rule constructor
	 * @param string $type Recommend using FormRule constant to determine rule types
	 * @param mixed $elements A single form element or an array of form elements
	 */
	function __construct($type, $element, $value=NULL, $validationMessage=NULL ) {
		//verify we have a single form element or an array of them
		if(is_array($element)===false){
			FormItBuilder::verifyFormElement($element);
		}else{
			foreach($element as $el){
				FormItBuilder::verifyFormElement($el);
			}
		}
		//main switch
		switch($type){
			
			//form field match, password confirm etc
			case FormRuleType::fieldMatch:
				if(is_array($element)===false || count($element)!==2){
					FormItBuilder::throwError('Rule "'.self::fieldMatch.'" must be applied to 2 elements (e.g. password and password_confirm). Pass 2 form elements in an array.');
				}
				if($validationMessage===NULL){
					$this->_validationMessage = $element[0]->getLabel().' must match '.$element[1]->getLabel();
				}
				break;
				
			//true false type validators
			case FormRuleType::email:
				if($validationMessage===NULL){
					 $this->_validationMessage = $element->getLabel().' must be a valid email address';
				}
				break;
			case FormRuleType::numeric:
				if($validationMessage===NULL){
					 $this->_validationMessage = $element->getLabel().' must be numeric';
				}
				break;
			case FormRuleType::required:
				if($validationMessage===NULL){
					$this->_validationMessage = $element->getLabel().' is required'; 
				}
				$element->isRequired(true);
				break;
				
			//value driven number type validators
			case FormRuleType::maximumLength:
				$value = FormItBuilder::forceNumber($value);
				if($validationMessage===NULL){
					
				}
				if(is_a($element, 'FormItBuilder_elementCheckboxGroup')){
					$this->_validationMessage = $element->getLabel().' must have less than '.($value+1).' selected';
				}else{
					$this->_validationMessage = $element->getLabel().' can only contain up to '.$value.' characters';
				}
					
				$element->setMaxLength($value);
				break;
			case FormRuleType::maximumValue:
				$value = FormItBuilder::forceNumber($value);
				if($validationMessage===NULL){
					$this->_validationMessage = $element->getLabel().' must not be greater than '.$value;
				}
				$element->setMaxValue($value);
				break;
			case FormRuleType::minimumLength:
				$value = FormItBuilder::forceNumber($value);
				if($validationMessage===NULL){
					if(is_a($element, 'FormItBuilder_elementCheckboxGroup')){
						$this->_validationMessage = $element->getLabel().' must have at least '.$value.' selected';
					}else{
						$this->_validationMessage = $element->getLabel().' must be at least '.$value.' characters';
					}
				}
				$element->setMinLength($value);
				break;
			case FormRuleType::minimumValue:
				$value = FormItBuilder::forceNumber($value);
				if($validationMessage===NULL){
					$this->_validationMessage = $element->getLabel().' must not be less than '.$value;
				}
				$element->setMinValue($value);
				break;	
			case FormRuleType::date:
				/*
				Supports any single character separator with any order of dd,mm and yyyy
				Example: yyyy-dd-mm dd$$mm$yyyy dd/yyyy/mm.
				Dates will be split and check if a real date is entered.
				*/
				$value=strtolower(trim($value));
				if(empty($value)===true){
					FormItBuilder::throwError('Date type field must have a value (date format) specified.');
				}
				if($validationMessage===NULL){
					$this->_validationMessage = $element->getLabel().' must be a valid date (===dateformat===).';
				}
				break;
			case FormRuleType::file:
				if($validationMessage===NULL){
					 $this->_validationMessage = $element->getLabel().' must be a valid file.';
				}
				break;			
			default:
				FormItBuilder::throwError('Type "'.$type.'" not valid. Recommend using FormRule constant');
				break;
		}
		
		$this->_type=$type;
		if($validationMessage!==NULL){
			$this->_validationMessage = $validationMessage;
		}
		$this->_element = $element;
		$this->_value = $value;
	}
}

?>
