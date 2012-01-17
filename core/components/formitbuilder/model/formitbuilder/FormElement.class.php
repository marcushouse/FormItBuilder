<?php
require_once 'FormItBuilderCore.class.php';

class FormItBuilder_htmlBlock extends FormItBuilderCore{
	private $_html;
	function __construct( string $html ) {		
		$this->_html=$html;
	}
	public function outputHTML(){
		return $this->_html;
	}
}
abstract class FormItBuilder_element extends FormItBuilderCore{
	
	protected $_id;
	protected $_label;
	
	protected $_showLabel;
	protected $_required;
	protected $_showInEmail;

	//Must include function to contsruct the html
	abstract protected function outputHTML();
	
	/**
	 * FormIt constructor
	 *
	 * @param modX &$modx A reference to the modX instance.
	 * @param array $config An array of configuration options. Optional.
	 */
	function __construct( string $id, string $label ) {		
		$this->_required = false;
		$this->_id = $id;
		$this->_label = $label;
		$this->_showLabel = true;
		$this->_showInEmail = true;
	}
	
	public function getId() { return $this->_id; }
	public function getLabel() { return $this->_label; }
        
	public function setId($v) { $this->_id = $v; }
	public function setLabel($v) { $this->_label = $v; }
        
	//single getter setter methods
	public function showLabel($v){
		if(func_num_args() == 0) {
			return $this->_showLabel;
		}else{
			$this->_showLabel = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
	public function isRequired($v){
		if(func_num_args() == 0) {
			return $this->_required;
		}else{
			$this->_required = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
	public function showInEmail($v){
		if(func_num_args() == 0) {
			return $this->_showInEmail;
		}else{
			$this->_showInEmail = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
}
class FormItBuilder_elementReCaptcha extends FormItBuilder_element{
	function __construct(string $label) {
		parent::__construct('recaptcha',$label);
		$this->_showInEmail=false;
	}
	public function outputHTML(){
		$s_ret='[[+formit.recaptcha_html]]';
		return $s_ret;
	}
}


class FormItBuilder_elementSelect extends FormItBuilder_element{
	private $_values;
	private $_defaultVal;
	/**
	 * FormIt constructor
	 *
	 * @param string $id Id of the element
	 * @param string $label Label of the select element
	 * @param array $values Array of title/value arrays in order of display.
	 */
	function __construct(string $id, string $label, array $values, string $defaultValue) {
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
	 * FormIt constructor
	 *
	 * @param string $id Id of the element
	 * @param string $label Label of the select element
	 * @param array $values Array of title/value arrays in order of display.
	 */
	function __construct(string $id, string $label, array $values, string $defaultValue) {
		parent::__construct($id,$label);
		$this->_values = $values;
		$this->_showIndividualLabels = true;
		$this->_defaultVal = $defaultValue;
	}
	
	public function showIndividualLabels($v){
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
	//TODO - Add getters and setters
	protected $_type;
	protected $_buttonLabel;
	protected $_src;

	/**
	 * FormIt constructor
	 *
	 * @param string $id Id of the button
	 * @param string $buttonLabel Label of the button
	 * @param string $type Type of button, e.g button, submit, reset etc.
	 */
	function __construct( string $id, string $buttonLabel, string $type ) {
		parent::__construct($id,$buttonLabel);
		$this->_showLabel = false;
		$this->_showInEmail = false;
		if($type=='button' || $type=='reset' || $type=='submit' || $type=='image'){
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
	//TODO - Add getters and setters
	private $_defaultVal;
	private $_rows;
	private $_cols;

	/**
	 * FormItBuilder_elementTextArea Constructor
	 * @param string $id Id of text area
	 * @param string $label Label of text area
	 * @param int $rows The required rows attribute value that must be set on a valid XHTML textarea tag.
	 * @param int $cols The required cols attribute value that must be set on a valid XHTML textarea tag.
	 * @param string $defaultValue Default text to appear in text area.
	 */
	function __construct( string $id, string $label, int $rows, int $cols, string $defaultValue=NULL) {
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
			$selectedStr=$this->_defaultVal;
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
	//TODO - Add getters and setters
	private $_value;
	private $_uncheckedValue;
	private $_checked;
	/**
	 * FormIt constructor
	 *
	 * @param string $id ID of checkbox
	 * @param string $label Label of checkbox
	 * @param string $value Value of checkbox
	 * @param boolean $checked Set as ticked by default
	 */
	function __construct( string $id, string $label, string $value=NULL, string $uncheckedValue=NULL, boolean $checked=NULL) {
		parent::__construct($id,$label);
		
		if($value===NULL){
			$this->_value='Ticked';
		}else{
			$this->_value=$value;
		}
		
		if($checked===NULL){
			$this->_checked=false;
		}else{
			$this->_checked=$checked;
		}
		
		if($uncheckedValue===NULL){
			$this->_uncheckedValue='Unticked';
		}else{
			$this->_uncheckedValue=$uncheckedValue;
		}
		
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
class FormItBuilder_elementText extends FormItBuilder_element{
	
	protected $_fieldType;
	
	protected $_maxLength;
	protected $_minLength;
	protected $_maxValue;
	protected $_minValue;
	protected $_dateFormat;
	/*
	protected $_isNumeric;
	protected $_isEmail;
	 */

	function __construct( string $id, string $label ) {
		parent::__construct($id,$label);
		$this->_maxLength=NULL;
		$this->_minLength=NULL;
		$this->_maxValue=NULL;
		$this->_minValue=NULL;
		
		/*
		$this->_isNumeric=false;
		$this->_isEmail=false;
		*/
		$this->_fieldType='text';
	}
	
	public function getMaxLength() { return $this->_maxLength; }
	public function getMinLength() { return $this->_minLength; }
	public function getMaxValue() { return $this->_maxValue; }
	public function getMinValue() { return $this->_minValue; }
	public function getDateFormat() { return $this->_dateFormat; }
	
	public function setMaxLength($v) {
		$v = FormItBuilder::forceNumber($v);
		if($this->_minLength!==NULL && $this->_minLength>$v){
			throw FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set maximum length to "'.$v.'" when minimum length is "'.$this->_minLength.'"');
		}else{
			$this->_maxLength = FormItBuilder::forceNumber($v);
		}
	}
	
	public function setMinLength($v) {
		$v = FormItBuilder::forceNumber($v);
		if($this->_maxLength!==NULL && $this->_maxLength<$v){
			FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set minimum length to "'.$v.'" when maximum length is "'.$this->_maxLength.'"');
		}else{
			if($this->_required===false){
				FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set minimum length to "'.$v.'" when field is not required.');
			}else{
				$this->_minLength = FormItBuilder::forceNumber($v);
			}
		}
	}
	
	public function setMaxValue($v) {
		$v = FormItBuilder::forceNumber($v);
		if($this->_minValue!==NULL && $this->_minValue>$v){
			FormItBuilder::throwError('Cannot set maximum value to "'.$v.'" when minimum value is "'.$this->_minValue.'"');
		}else{
			$this->_maxValue = FormItBuilder::forceNumber($v);
		}
	}
	public function setMinValue($v) {
		$v = FormItBuilder::forceNumber($v);
		if($this->_maxValue!==NULL && $this->_maxValue<$v){
			FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set minimum value to "'.$v.'" when maximum value is "'.$this->_maxValue.'"');
		}else{
			$this->_minValue = FormItBuilder::forceNumber($v);
		}
	}
	
	public function setDateFormat($v) {
		$v=trim($v);
		if(empty($v)===true){
			FormItBuilder::throwError('[Element: '.$this->_id.'] Date format is not valid.');
		}else{
			$this->_dateFormat=$v;
		}
	}
	
	
	/*
	public function isEmail($v){
		if(func_num_args() == 0) {
			return $this->_isEmail;
		}else{
			$this->_isEmail = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
	public function isNumeric($v){
		if(func_num_args() == 0) {
			return $this->_isNumeric;
		}else{
			$this->_isNumeric = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
	*/
	
	public function outputHTML(){
		$a_classes=array();
		$s_ret='<input type="'.$this->_fieldType.'" name="'.htmlspecialchars($this->_id).'" id="'.htmlspecialchars($this->_id).'" value="[[+fi.'.htmlspecialchars($this->_id).']]"';
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
	function __construct( string $id, string $label ) {
		parent::__construct($id,$label);
		$this->_fieldType='password';
	}
}
class FormItBuilder_elementFile extends FormItBuilder_elementText{
	function __construct( string $id, string $label ) {
		parent::__construct($id,$label);
		$this->_fieldType='file';
	}
}

?>