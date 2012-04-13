<?php
require_once 'FormItBuilderCore.class.php';

/**
 * A primitive form element used as a base to extend into a variety of elements
 */
class FormItBuilder_baseElement extends FormItBuilderCore{
}

/**
 * A primitive form element used only to inject raw html and place between other elements.
 */
class FormItBuilder_htmlBlock extends FormItBuilder_baseElement{
	private $_html;
	/**
	 * Creates a segment of the specified html. This is great for introducing your own separators or wrappers around other elements in the form.
	 * @param string $html The html code to use as the element
	 */
	function __construct( $html ) {		
		$this->_html=$html;
	}
	
	/**
	 * output function called when generating the form elements content.
	 * @return type 
	 */
	public function outputHTML(){
		return $this->_html;
	}
}

abstract class FormItBuilder_element extends FormItBuilder_baseElement{
	
	protected $_id;
	protected $_name; //usually the same as the id, but not in the case of checkbox group that uses array syntax for name.
	protected $_label;
	protected $_description;
	
	protected $_showLabel;
	protected $_required;
	protected $_showInEmail;

	/**
	 * output function called when generating the form element content.
	 * @return type 
	 */
	abstract protected function outputHTML();
	
	/**
	 * FormIt constructor
	 *
	 * @param modX &$modx A reference to the modX instance.
	 * @param array $config An array of configuration options. Optional.
	 */
	function __construct( $id, $label ) {		
		$this->_required = false;
		$this->_id = $this->_name = $id;
		$this->_label = $label;
		$this->_showLabel = true;
		$this->_showInEmail = true;
		$this->_description = NULL; //must be set by setDescription
	}
	
	public function getId() { return $this->_id; }
	public function getName() { return $this->_name; }
	public function getLabel() { return $this->_label; }
	public function getDescription() { return $this->_description; }
        
	public function setId($value) { $this->_id = $value; }
	public function setName($value) { $this->_name = $value; }
	public function setLabel($value) { $this->_label = $value; }
	/**
	 * Allows a sub label (or more descriptive label) to be set within the element label. Could be shown on hover or displayed with main label.
	 * @return type 
	 */
	public function setDescription($value) { $this->_description = $value; }
        
	//single getter setter methods
	public function showLabel($value=null){
		if(func_num_args() == 0) {
			return $this->_showLabel;
		}else{
			$this->_showLabel = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
	public function isRequired($value=null){
		if(func_num_args() == 0) {
			return $this->_required;
		}else{
			$this->_required = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
	public function showInEmail($value=null){
		if(func_num_args() == 0) {
			return $this->_showInEmail;
		}else{
			$this->_showInEmail = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
}
class FormItBuilder_elementReCaptcha extends FormItBuilder_element{
	/**
	 * Creates a recaptcha field with the FormIt integrated recaptcha systems
	 * @param type $label Label for the recaptcha
	 */
	protected $_jsonConfig;
	
	function __construct($label) {
		parent::__construct('recaptcha',$label);
		$this->_showInEmail=false;
	}
	public function outputHTML(){
		$s_ret='[[+formit.recaptcha_html]]';
		return $s_ret;
	}
	/**
	 * Allows the setting of reCaptcha config. See https://developers.google.com/recaptcha/docs/customization for more information
	 * @param type $jsonString 
	 */
	public function setJsonConfig($jsonString){
		$this->_jsonConfig=$jsonString;
	}	
	public function getJsonConfig(){
		return $this->_jsonConfig;
	}	
	
}


class FormItBuilder_elementSelect extends FormItBuilder_element{
	private $_values;
	private $_defaultVal;
	/**
	 * Creates a select dropdown element
	 *
	 * <code>
	 * $a_usstates = array(
	 *	''=>'Please select...',
	 *	'AL'=>'Alabama',
	 *	'AK'=>'Alaska',
	 *	'AZ'=>'Arizona',
	 *	'AR'=>'Arkansas',
	 *	'CA'=>'California',
	 *	'CO'=>'Colorado',
	 *	'CT'=>'Connecticut'
	 * );
	 * $o_fe_usstates = new FormItBuilder_elementSelect('ussuate','Select a state',$a_usstates,'AR');
	 * </code>
	 * 
	 * @param string $id The ID of the element
	 * @param string $label The label of the select element
	 * @param array $values An array of title/value arrays in order of display
	 * @param string $defaultValue The default value to select in the dropdown field 
	 */
	function __construct($id, $label, array $values, $defaultValue=null) {
		parent::__construct($id,$label);
		$this->_values = $values;
		$this->_defaultVal = $defaultValue;
	}
	
	public function outputHTML(){
		if(isset($_POST[$this->_id])===true){
			$selectedVal=$_POST[$this->_id];
		}else{
			$selectedVal=$this->_defaultVal;
		}
		$b_selectUsed=false;
		$s_ret='<select id="'.htmlspecialchars($this->_id).'" name="'.htmlspecialchars($this->_id).'">'."\r\n";
		foreach($this->_values as $key=>$value){
			$selectedStr='';
			if(isset($_POST[$this->_id])===true){
				$selectedStr='[[!+fi.'.htmlspecialchars($this->_id).':FormItIsSelected=`'.htmlspecialchars($key).'`]]';
			}else{
				if($this->_defaultVal==$key){
					$selectedStr=' selected="selected"';
				}
			}
			$s_ret.='<option value="'.htmlspecialchars($key).'"'.$selectedStr.'>'.htmlspecialchars($value).'</option>'."\r\n";
		}
		$s_ret.='</select>';
		return $s_ret;
	}
}
class FormItBuilder_elementRadioGroup extends FormItBuilder_element{
	private $_values;
	private $_defaultVal;
	private $_showIndividualLabels;
	/**
	 * Creates a group of radio button elements
	 * <code>
	 * $a_performanceOptions = array(
	 *	'opt1'=>'Poor',
	 *	'opt2'=>'Needs Improvement',
	 *	'opt3'=>'Average',
	 *	'opt4'=>'Good',
	 *	'opt5'=>'Excellent'
	 * );
	 * $o_fe_staff = new FormItBuilder_elementRadioGroup('staff_performance','How would you rate staff performance?',$a_performanceOptions,'opt3');
	 * </code>
	 *
	 * @param string $id The ID of the element
	 * @param string $label The label of the select element
	 * @param array $values An array of title/value arrays in order of display
	 * @param string $defaultValue The value of the default selected radio option
	 */
	function __construct($id, $label, array $values, $defaultValue=null) {
		parent::__construct($id,$label);
		$this->_values = $values;
		$this->_showIndividualLabels = true;
		$this->_defaultVal = $defaultValue;
	}
	
	public function showIndividualLabels($value){
		if(func_num_args() == 0) {
			return $this->_showIndividualLabels;
		}else{
			$this->_showIndividualLabels = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
	
	public function outputHTML(){
		$s_ret='<div class="radioGroupWrap">';
		$i=0;
		foreach($this->_values as $key=>$value){
			$s_ret.='<div class="radioWrap">';
			if($this->_showIndividualLabels===true){
				$s_ret.='<label for="'.htmlspecialchars($this->_id.'_'.$i).'">'.htmlspecialchars($value).'</label>';
			}
			$s_ret.='<div class="radioEl"><input type="radio" id="'.htmlspecialchars($this->_id.'_'.$i).'" name="'.htmlspecialchars($this->_id).'" value="'.htmlspecialchars($key).'"';
			$selectedStr='';
			if(isset($_POST[$this->_id])===true){
				$selectedStr='[[!+fi.'.htmlspecialchars($this->_id).':FormItIsChecked=`'.htmlspecialchars($key).'`]]';
			}else{
				if($this->_defaultVal==$key){
					$selectedStr=' checked="checked"';
				}
			}
			$s_ret.=$selectedStr.' /></div></div>'."\r\n";
			$i++;
		}
		$s_ret.='</div>';
		return $s_ret;
	}
}
class FormItBuilder_elementButton extends FormItBuilder_element{
	protected $_type;
	protected $_buttonLabel;
	protected $_src;

	/**
	 * Creates a form button element
	 *
	 * @param string $id The ID of the button
	 * @param string $buttonLabel The label of the button
	 * @param string $type The button type, e.g button, image, reset, submit etc.
	 */
	function __construct($id, $buttonLabel, $type ) {
		parent::__construct($id,$buttonLabel);
		$this->_showLabel = false;
		$this->_showInEmail = false;
		if($type=='button' || $type=='image' || $type=='reset' || $type=='submit'){
			//ok -- valid type
		}else{
			FormItBuilder::throwError('[Element: '.$this->_id.'] Button "'.htmlspecialchars($type).'" must be of type "button", "reset", "image" or "submit"');
		}
		$this->_type = $type;
	}
	
	public function outputHTML(){
		$s_ret='<input id="'.htmlspecialchars($this->_id).'" type="'.htmlspecialchars($this->_type).'" value="'.htmlspecialchars($this->_label).'"';
		if($this->_type=='image'){
			if($this->_src===NULL){
				FormItBuilder::throwError('[Element: '.$this->_id.'] Button of type "image" must have a src set.');
			}else{
				$s_ret.=' src="'.htmlspecialchars($this->_src).'"';
			}
		}
		$s_ret.=' />';
		return $s_ret;
	}
}

class FormItBuilder_elementTextArea extends FormItBuilder_element{
	private $_defaultVal;
	private $_rows;
	private $_cols;

	/**
	 * Creates a text area element.
	 * @param string $id ID of text area
	 * @param string $label The label of text area
	 * @param int $rows The required rows (attribute value that must be set on a valid XHTML textarea tag)
	 * @param int $cols The required cols (attribute value that must be set on a valid XHTML textarea tag)
	 * @param string $defaultValue The default text to be written into the text area
	 */
	function __construct($id, $label, $rows, $cols, $defaultValue=NULL) {
		parent::__construct($id,$label);
		$this->_defaultVal = $defaultValue;
		$this->_rows = FormItBuilderCore::forceNumber($rows);
		$this->_cols = FormItBuilderCore::forceNumber($cols);
	}
	
	public function outputHTML(){
		//hidden field with same name is so we get a post value regardless of tick status
		if(isset($_POST[$this->_id])===true){
			$selectedStr='[[!+fi.'.htmlspecialchars($this->_id).']]';
		}else{
			$selectedStr=htmlspecialchars($this->_defaultVal);
		}
		if($this->_required===true){
			$a_classes[]='required'; // for jquery validate (or for custom CSSing :) )
		}
		
		$s_ret='<textarea id="'.htmlspecialchars($this->_id).'" rows="'.htmlspecialchars($this->_rows).'" cols="'.htmlspecialchars($this->_cols).'" name="'.htmlspecialchars($this->_id).'"';
		//add classes last
		if(count($a_classes)>0){
			$s_ret.=' class="'.implode(' ',$a_classes).'"';
		}
		$s_ret.='>'.$selectedStr.'</textarea>';
		return $s_ret;
	}
}

class FormItBuilder_elementCheckbox extends FormItBuilder_element{
	private $_value;
	private $_uncheckedValue;
	private $_checked;
	/**
	 * FormIt constructor
	 *
	 * @param string $id ID of checkbox
	 * @param string $label Label of checkbox
	 * @param string $value Value to show if user selects the checkbox
	 * @param boolean $uncheckedValue Value to show if user does not check the checkbox
	 * @param boolean $checked Make checkbox ticked by default
	 */
	function __construct( $id, $label, $value='Checked', $uncheckedValue='Unchecked', $checked=false) {
		parent::__construct($id,$label);
		$this->_value=$value;
		$this->_checked=$checked;
		$this->_uncheckedValue=$uncheckedValue;		
	}
	
	public function outputHTML(){
		$a_uncheckedVal = $this->_uncheckedValue;
		if($this->_required===true){
			$a_uncheckedVal=''; // we do this because FormIt will not validate it as empty if unchecked value has a value.
		}
		//hidden field with same name is so we get a post value regardless of tick status
		$s_ret='<input type="hidden" name="'.htmlspecialchars($this->_id).'" value="'.htmlspecialchars($a_uncheckedVal).'" />'
		.'<input type="checkbox" id="'.htmlspecialchars($this->_id).'" name="'.htmlspecialchars($this->_id).'" value="'.htmlspecialchars($this->_value).'" [[!+fi.'.htmlspecialchars($this->_id).':FormItIsChecked=`'.htmlspecialchars($this->_value).'`]] />';
		return $s_ret;
	}
}

class FormItBuilder_elementCheckboxGroup extends FormItBuilder_element{
	//Thanks Michelle
	private $_values;
	private $_showIndividualLabels;
	private $_uncheckedValue;
	private $_maxLength;
	private $_minLength;
	
	
	/**
	 * Creates a group of checkboxes that allow rules such as required, minimum length (minimum number of items that must be checked) and maximum length (maximum number of items that can be checked). The list of checkbox values are specified in an array along with their default ticked state.
	 * 
	 * <code>
	 * $a_checkArray=array(
	 *	array('title'=>'Cheese','checked'=>false),
	 *	array('title'=>'Grapes','checked'=>true),
	 *	array('title'=>'Salad','checked'=>false),
	 *	array('title'=>'Bread','checked'=>true)
	 * );
	 * $o_fe_checkgroup		= new FormItBuilder_elementCheckboxGroup('favFoods','Favorite Foods',$a_checkArray);
	 * //Ensure at least 2 checkboxes are selected
	 * $a_formRules[] = new FormRule(FormRuleType::minimumLength,$o_fe_checkgroup,2);
	 * //Ensure no more than 3 checkboxes are selected
	 * $a_formRules[] = new FormRule(FormRuleType::maximumLength,$o_fe_checkgroup,3);
	 * </code>
	 *
	 * @param string $id Id of the element
	 * @param string $label Label of the select element
	 * @param array $values Array of title/value arrays in order of display.
	 */
	function __construct($id, $label, array $values) {
		parent::__construct($id,$label);
		$this->_name = $id.'[]';
		$this->_values = $values;
		$this->_showIndividualLabels = true;
		$this->_uncheckedValue = 'None Selected';
	}
	
	public function showIndividualLabels($value){
		if(func_num_args() == 0) {
			return $this->_showIndividualLabels;
		}else{
			$this->_showIndividualLabels = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
	
	public function setMinLength($value) {
		$value = FormItBuilder::forceNumber($value);
		if($this->_maxLength!==NULL && $this->_maxLength<$value){
			FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set minimum length to "'.$value.'" when maximum length is "'.$this->_maxLength.'"');
		}else{
			if($this->_required===false){
				FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set minimum length to "'.$value.'" when field is not required.');
			}else{
				$this->_minLength = FormItBuilder::forceNumber($value);
			}
		}
	}
	
	public function setMaxLength($value) {
		$value = FormItBuilder::forceNumber($value);
		if($this->_minLength!==NULL && $this->_minLength>$value){
			throw FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set maximum length to "'.$value.'" when minimum length is "'.$this->_minLength.'"');
		}else{
			$this->_maxLength = FormItBuilder::forceNumber($value);
		}
	}
	
	public function outputHTML(){
		$s_ret='<div class="checkboxGroupWrap">';
		$i=0;
		
		$a_uncheckedVal = $this->_uncheckedValue;
		if($this->_required===true){
			$a_uncheckedVal=''; // we do this because FormIt will not validate it as empty if unchecked value has a value.
		}
		//hidden field with same name is so we get a post value regardless of tick status, must use ID and not name
		$s_ret.='<input type="hidden" name="'.htmlspecialchars($this->_id).'" value="'.htmlspecialchars($a_uncheckedVal).'" />';
				
		foreach($this->_values as $value){
			$s_ret.='<div class="checkboxWrap">';
			if($this->_showIndividualLabels===true){
				$s_ret.='<label for="'.htmlspecialchars($this->_id.'_'.$i).'">'.htmlspecialchars($value['title']).'</label>';
			}
			// changed input type to checkbox
			// added [] to name
			$s_ret.='<div class="checkboxEl"><input type="checkbox" id="'.htmlspecialchars($this->_id.'_'.$i).'" name="'.htmlspecialchars($this->_name).'" value="'.htmlspecialchars($value['title']).'"';
			$selectedStr='';
			if(isset($_POST[$this->_id])===true){
				if(in_array($value['title'],$_POST[$this->_id])===true){
					$selectedStr='[[!+fi.'.htmlspecialchars($this->_id).':FormItIsChecked=`'.htmlspecialchars($value['title']).'`]]';
				}
			}else{
				if(isset($value['checked'])===true && $value['checked']===true){
					$selectedStr=' checked="checked"';
				}
			}
			$s_ret.=$selectedStr.' /></div></div>'."\r\n";
			$i++;
		}
		$s_ret.='</div>';
		return $s_ret;
	}
}

class FormItBuilder_elementText extends FormItBuilder_element{
	
	protected $_fieldType;
	
	protected $_maxLength;
	protected $_minLength;
	protected $_maxValue;
	protected $_minValue;
	protected $_dateFormat;
	protected $_defaultVal;

	/**
	 * Creates a text field.
	 * @param type $id The ID of the text field
	 * @param type $label The label of the text field
	 * @param type $defaultValue The default text to be written into the text field
	 */
	function __construct( $id, $label, $defaultValue=NULL ) {
		parent::__construct($id,$label);
		$this->_defaultVal = $defaultValue;
		$this->_maxLength=NULL;
		$this->_minLength=NULL;
		$this->_maxValue=NULL;
		$this->_minValue=NULL;
		$this->_fieldType='text';
	}
	
	public function getMaxLength() { return $this->_maxLength; }
	public function getMinLength() { return $this->_minLength; }
	public function getMaxValue() { return $this->_maxValue; }
	public function getMinValue() { return $this->_minValue; }
	public function getDateFormat() { return $this->_dateFormat; }
	
	public function setMaxLength($value) {
		$value = FormItBuilder::forceNumber($value);
		if($this->_minLength!==NULL && $this->_minLength>$value){
			throw FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set maximum length to "'.$value.'" when minimum length is "'.$this->_minLength.'"');
		}else{
			$this->_maxLength = FormItBuilder::forceNumber($value);
		}
	}
	
	public function setMinLength($value) {
		$value = FormItBuilder::forceNumber($value);
		if($this->_maxLength!==NULL && $this->_maxLength<$value){
			FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set minimum length to "'.$value.'" when maximum length is "'.$this->_maxLength.'"');
		}else{
			if($this->_required===false){
				FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set minimum length to "'.$value.'" when field is not required.');
			}else{
				$this->_minLength = FormItBuilder::forceNumber($value);
			}
		}
	}
	
	public function setMaxValue($value) {
		$value = FormItBuilder::forceNumber($value);
		if($this->_minValue!==NULL && $this->_minValue>$value){
			FormItBuilder::throwError('Cannot set maximum value to "'.$value.'" when minimum value is "'.$this->_minValue.'"');
		}else{
			$this->_maxValue = FormItBuilder::forceNumber($value);
		}
	}
	public function setMinValue($value) {
		$value = FormItBuilder::forceNumber($value);
		if($this->_maxValue!==NULL && $this->_maxValue<$value){
			FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set minimum value to "'.$value.'" when maximum value is "'.$this->_maxValue.'"');
		}else{
			$this->_minValue = FormItBuilder::forceNumber($value);
		}
	}
	
	public function setDateFormat($value) {
		$value=trim($value);
		if(empty($value)===true){
			FormItBuilder::throwError('[Element: '.$this->_id.'] Date format is not valid.');
		}else{
			$this->_dateFormat=$value;
		}
	}
	
	public function outputHTML(){
		$a_classes=array();
		
		//hidden field with same name is so we get a post value regardless of tick status
		if(isset($_POST[$this->_id])===true){
			$selectedStr='[[!+fi.'.htmlspecialchars($this->_id).']]';
		}else{
			$selectedStr=htmlspecialchars($this->_defaultVal);
		}
		
		$s_ret='<input type="'.$this->_fieldType.'" name="'.htmlspecialchars($this->_id).'" id="'.htmlspecialchars($this->_id).'" value="'.$selectedStr.'"';
		if($this->_maxLength!==NULL){
			$s_ret.=' maxlength="'.htmlspecialchars($this->_maxLength).'"';
		}
		if($this->_required===true){
			$a_classes[]='required'; // for jquery validate (or for custom CSSing :) )
		}
		//add classes last
		if(count($a_classes)>0){
			$s_ret.=' class="'.implode(' ',$a_classes).'"';
		}
		$s_ret.=' />';
		return $s_ret;
	}
}
class FormItBuilder_elementPassword extends FormItBuilder_elementText{
	/**
	 * Creates a password field.
	 * @param type $id The ID of the password field
	 * @param type $label The label of the password field
	 * @param type $defaultValue The default text to be written into the password field
	 */
	function __construct( $id, $label, $defaultValue=NULL ) {
		parent::__construct($id,$label,$defaultValue);
		$this->_fieldType='password';
	}
}
class FormItBuilder_elementHidden extends FormItBuilder_elementText{
	/**
	 * Creates a hidden field.
	 * @param type $id The ID of the hidden field
	 * @param type $label The label of the hidden field
	 * @param type $defaultValue The default value to be written into the hidden field
	 */
	function __construct( $id, $label, $defaultValue=NULL ) {
		parent::__construct($id,$label,$defaultValue);
		$this->_fieldType='hidden';
		$this->_showInEmail=false;
	}
	public function showInEmail($value=null){
		if(func_num_args() == 0) {
			return $this->_showInEmail;
		}else{
			$this->_showInEmail = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
}
class FormItBuilder_elementFile extends FormItBuilder_elementText{
	/**
	 * Creates a file field element allowing upload of file to the server (and attached to email)
	 * @param type $id The ID of the file element
	 * @param type $label The label of the file element
	 */
	function __construct( $id, $label ) {
		parent::__construct($id,$label);
		$this->_fieldType='file';
	}
}

?>