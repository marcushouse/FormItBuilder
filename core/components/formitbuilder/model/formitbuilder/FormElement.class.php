<?php
/**
 * Contains all the form element classes.
 * @package FormItBuilder
 */

/**
 * Required Files
 */
require_once dirname(__FILE__).'/FormItBuilderCore.class.php';


/**
 * A primitive form element used as a base to extend into a variety of elements
 * @package FormItBuilder
 */
class FormItBuilder_baseElement extends FormItBuilderCore{
	/**
	 * @ignore 
	 */
	private $_customObject;
	/**
	 * setCustomObject($value)
	 * 
	 * Not used via FormItBuilder in anyway, however lets users attach object references or data into this property and then retrive the data via script. Very much a power user feature.
	 * @param mixed $value Can set anything here.
	 */
	public function setCustomObject($value) { $this->_customObject = $value; }
	
	/**
	 * getCustomObject()
	 * 
	 * Returns the custom object.
	 * @return mixed
	 */
	public function getCustomObject() { return $this->_customObject; }
}

/**
 * A primitive form element used only to inject raw html and place between other elements.
 * @package FormItBuilder
 */
class FormItBuilder_htmlBlock extends FormItBuilder_baseElement{
	/**
	 * @ignore 
	 */
	private $_html;
	/**
	 * FormItBuilder_htmlBlock
	 * 
	 * Creates a segment of the specified html. This is great for introducing your own separators or wrappers around other elements in the form.
	 * @param string $html The html code to use as the element
	 */
	function __construct( $html ) {		
		$this->_html=$html;
	}
	
	/**
	 * outputHTML()
	 * 
	 * output function called when generating the form elements content.
	 * @return string
	 */
	public function outputHTML(){
		return $this->_html;
	}
}

/**
 * A less primitive form element used as a base to extend into a variety of elements containing properties and methods used by all physical form elements.
 * @package FormItBuilder
 */
abstract class FormItBuilder_element extends FormItBuilder_baseElement{
	/**
	 * @ignore
	 */
	protected $_id;
	/**
	 * Usually the same as the id, but not in the case of checkbox group that uses array syntax for name.
	 * @ignore
	 */
	protected $_name; //
	/**
	 * @ignore
	 */
	protected $_label;
	/**
	 * @ignore
	 */
	protected $_description;
	/**
	 * @ignore
	 */
	protected $_showLabel;
	/**
	 * @ignore
	 */
	protected $_required;
	/**
	 * @ignore
	 */
	protected $_showInEmail;
	/**
	 * @ignore
	 */
	protected $_extraClasses;
	/**
	 * @ignore
	 */
	protected $_labelAfterElement;

	/**
	 * output function called when generating the form element content.
	 * @return string
	 */
	abstract protected function outputHTML();
	
	/**
	 * FormItBuilder_element
	 * 
	 * Main element constructor.
	 * @param string $id
	 * @param string $label 
	 */
	function __construct( $id, $label ) {		
		$this->_required = false;
		$this->_id = $this->_name = $id;
		$this->_label = $label;
		$this->_showLabel = true;
		$this->_showInEmail = true;
		$this->_description = NULL; //must be set by setDescription
		$this->_extraClasses = NULL;
		$this->_labelAfterElement=false;
	}
	
	/**
	 * getId()
	 * 
	 * Returns the form elements ID.
	 * @return string
	 */
	public function getId() { return $this->_id; }
	/**
	 * getName()
	 * 
	 * Returns the form elements name.
	 * @return string
	 */
	public function getName() { return $this->_name; }
	/**
	 * getLabel()
	 * 
	 * Returns the form elements label.
	 * @return string 
	 */
	public function getLabel() { return $this->_label; }
	/**
	 * getDescription()
	 * 
	 * Returns the form elements description.
	 * @return string 
	 */
	public function getDescription() { return $this->_description; }
	/**
	 * getExtraClasses()
	 * 
	 * Returns the array of extra classes applied to the element.
	 * @return array
	 */
	public function getExtraClasses() { return $this->_extraClasses; }
	/**
	 * getLabelAfterElement()
	 * 
	 * Returns a boolean indicating if an element will output label HTML before (false) or after (true) the element.
	 * @return boolean
	 */
	public function getLabelAfterElement() { return $this->_labelAfterElement; }
	
	
	/**
	 * setId($value)
	 * 
	 * Sets the form elements ID.
	 * @param string $value 
	 */
	public function setId($value) { $this->_id = $value; }
	/**
	 * setName($value)
	 * 
	 * Sets the form elements name.
	 * @param string $value 
	 */
	public function setName($value) { $this->_name = $value; }
	/**
	 * setLabel($value)
	 * 
	 * Sets the form elements label.
	 * @param string $value 
	 */
	public function setLabel($value) { $this->_label = $value; }
	/**
	 * setDescription($value)
	 * 
	 * Allows a sub label (or more descriptive label) to be set within the element label. Could be shown on hover or displayed with main label.
	 * @param string $value 
	 */
	public function setDescription($value) { $this->_description = $value; }
	/**
	 * setExtraClasses($value)
	 * 
	 * Allows you to add your own classes on the wrapper element so you can apply specific CSS commands to one or more fields.
	 * @param array $value An array of class strings.
	 */
	public function setExtraClasses($value) { $this->_extraClasses = self::forceArray($value); }
	
	/**
	 * setLabelAfterElement($value)
	 * 
	 * By default all labels are output before the element itself. Although CSS can position one in from of the other it can cause extra frustration when trying to align elements in a variety of ways. By setting this property to true you can force an element to output the lable after the element.
	 * @param boolean $value If true label HTML will output after the element instead of before.
	 */
	public function setLabelAfterElement($value) { $this->_labelAfterElement = self::forceBool($value); }
	
        
	/**
	 * showLabel($value) / showLabel()
	 * 
	 * Sets wether the label should be displayed for this element or not. If no value passed the method will return the current status.
	 * @param boolean $value If true (which is in most cases default) a label will be shown next to the form element and in the email.
	 * @return boolean 
	 */
	public function showLabel($value=null){
		if(func_num_args() == 0) {
			return $this->_showLabel;
		}else{
			$this->_showLabel = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
	/**
	 * isRequired($value) / isRequired()
	 * 
	 * Sets wether the element is required or not.
	 * 
	 * This setting is generally toggled automatically based on FormRule settings applied in the form.
	 * 
	 * If no value passed the method will return the current required status.
	 * 
	 * @param boolean $value If true the field must be filled out.
	 * @return boolean 
	 */
	public function isRequired($value=null){
		if(func_num_args() == 0) {
			return $this->_required;
		}else{
			$this->_required = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
	/**
	 * showInEmail($value) / showInEmail()
	 * 
	 * Sets wether the element is shown in the email or not. 
	 * 
	 * In some cases fields may be wanted in the form, but not in the email
	 * (an example would be fields like a "Confirm Password" field).
	 * 
	 * If no value passed the method will return the current status.
	 * 
	 * @param boolean $value If true (which is in most cases default) the element will be shown in the email.
	 * @return boolean
	 */
	public function showInEmail($value=null){
		if(func_num_args() == 0) {
			return $this->_showInEmail;
		}else{
			$this->_showInEmail = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
}
/**
 * Creates a recaptcha field with the FormIt integrated recaptcha systems.
 * @package FormItBuilder
 */
class FormItBuilder_elementReCaptcha extends FormItBuilder_element{
	/**
	 * @ignore
	 */
	protected $_jsonConfig;
	
	/**
	 * FormItBuilder_elementReCaptcha
	 * 
	 * Constructor for the FormItBuilder_elementReCaptcha object.
	 * @param string $label 
	 */
	function __construct($label) {
		parent::__construct('recaptcha',$label);
		$this->_showInEmail=false;
	}
	/**
	 * outputHTML()
	 * 
	 * Outputs the HTML for the element.
	 * @return string 
	 */
	public function outputHTML(){
		$s_ret='[[+formit.recaptcha_html]]';
		return $s_ret;
	}
	/**
	 * setJsonConfig($jsonString)
	 * 
	 * Allows the setting of reCaptcha config. See https://developers.google.com/recaptcha/docs/customization for more information
	 * @param string $jsonString 
	 */
	public function setJsonConfig($jsonString){
		$this->_jsonConfig=$jsonString;
	}
	/**
	 * getJsonConfig()
	 * 
	 * Returns the reCaptcha JSON config.
	 * @return string
	 */
	public function getJsonConfig(){
		return $this->_jsonConfig;
	}	
	
}

/**
 * Creates a select (dropdown) field element.
 * @package FormItBuilder
 */
class FormItBuilder_elementSelect extends FormItBuilder_element{
	/**
	 * @ignore 
	 */
	private $_values;
	/**
	 * @ignore 
	 */
	private $_defaultVal;
	
	/**
	 * FormItBuilder_elementSelect
	 * 
	 * Creates a select dropdown element.
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
	
	/**
	 * outputHTML()
	 * 
	 * Outputs the HTML for the element.
	 * @return string 
	 */
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
				if($_POST[$this->_id]==$key){
					$selectedStr=' selected="selected"';
				}
			}else{
				if($this->_defaultVal==$key){
					$selectedStr=' selected="selected"';
				}
			}
			//trims here so we can force space
			$s_ret.='<option value="'.htmlspecialchars(trim($key)).'"'.$selectedStr.'>'.htmlspecialchars($value).'</option>'."\r\n";
		}
		$s_ret.='</select>';
		return $s_ret;
	}
}

/**
 * Creates a radio button group.
 * @package FormItBuilder
 */
class FormItBuilder_elementRadioGroup extends FormItBuilder_element{
	/**
	 * @ignore 
	 */
	private $_values;
	/**
	 * @ignore 
	 */
	private $_defaultVal;
	/**
	 * @ignore 
	 */
	private $_showIndividualLabels;
	
	/**
	 * FormItBuilder_elementRadioGroup
	 * 
	 * Creates a group of radio button elements.
	 * 
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
	
	/**
	 * showIndividualLabels($value)
	 * 
	 * By default (true) each radio option will have its own label. Users may wish to have radio options in some kind of table with custom surrounding HTML. In this case labels can be hidden.  If no value passed the method will return the current label display status. 
	 * @param boolean $value
	 * @return boolean 
	 */
	public function showIndividualLabels($value){
		if(func_num_args() == 0) {
			return $this->_showIndividualLabels;
		}else{
			$this->_showIndividualLabels = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
	/**
	 * outputHTML()
	 * 
	 * Outputs the HTML for the element.
	 * @return string 
	 */
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
				if($_POST[$this->_id]==$key){
					$selectedStr=' checked="checked"';
				}
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

/**
 * Creates a form button element (e.g button, image, reset, submit)
 * @package FormItBuilder
 */
class FormItBuilder_elementButton extends FormItBuilder_element{
	/**
	 * @ignore
	 */
	protected $_type;
	/**
	 * @ignore
	 */
	protected $_buttonLabel;
	/**
	 * @ignore
	 */
	protected $_src;

	/**
	 * FormItBuilder_elementButton
	 * 
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
	/**
	 * outputHTML()
	 * 
	 * Outputs the HTML for the element.
	 * @return string 
	 */
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

class FormItBuilder_elementMatrix extends FormItBuilder_element{
	private $a_rows;
	private $a_columns;
	private $s_type;
	
	function __construct($id, $label, $type, $rowLabels, $columnLabels ){
		parent::__construct($id,$label);
		$this->a_columns = self::forceArray($columnLabels);
		$this->a_rows  = self::forceArray($rowLabels);
		if($type!='select'&&$type!='text'&&$type!='radio'&&$type!='check'){
			FormItBuilder::throwError('[Element: '.$this->_id.'] Not a valid type, must be "select", "text", "radio" or "check".');
		}else{
			$this->s_type = $type;
		}
	}
	
	/**
	 * getType()
	 * 
	 * Returns the matrix type ("select", "text", "radio" or "check")
	 * @return string
	 */
	public function getType() { return $this->s_type; }
	/**
	 * getRows()
	 * 
	 * Returns the array of row labels
	 * @return array 
	 */
	public function getRows() { return $this->a_rows; }
	/**
	 * getColumns()
	 * 
	 * Returns the array of column labels
	 * @return array 
	 */
	public function getColumns() { return $this->a_columns; }
	
	/**
	 * outputHTML()
	 * 
	 * Outputs the HTML for the element.
	 * @return string 
	 */
	public function outputHTML(){
		$s_ret.='<input type="hidden" name="'.htmlspecialchars($this->_id).'_gridInfo" value="'.count($this->a_rows).','.count($this->a_columns).'" />';
		$s_ret.='<table class="matrix '.$this->s_type.'"><tr><th class="spaceCell">&nbsp;</th>';
		foreach($this->a_columns as $column){
			$s_ret.='<th class="columnHead">'.htmlspecialchars($column).'</th>';
		}
		$s_ret.='</tr>';
		$r_cnt=0;
		foreach($this->a_rows as $row){
			$s_ret.='<tr><th class="rowHead">'.htmlspecialchars($row).'</th>';
			$c_cnt=0;
			foreach($this->a_columns as $column){
				switch($this->s_type){
					case 'text':
						$el=new FormItBuilder_elementText($this->_id.'_'.$r_cnt.'_'.$c_cnt,'');
						$s_cellHTML=$el->outputHTML();
						break;
					case 'radio':
						$s_cellHTML='<input '.(isset($_POST[$this->_id.'_'.$r_cnt]) && ($_POST[$this->_id.'_'.$r_cnt]==$c_cnt)?'checked="checked" ':'').'type="radio" id="'.htmlspecialchars($this->_id.'_'.$r_cnt.'_'.$c_cnt).'" name="'.htmlspecialchars($this->_id.'_'.$r_cnt).'" value="'.htmlspecialchars($c_cnt).'" />';
						break;
					case 'check':
						$s_cellHTML='<input '.(isset($_POST[$this->_id.'_'.$r_cnt]) && in_array($c_cnt,$_POST[$this->_id.'_'.$r_cnt])===true?'checked="checked" ':'').'type="checkbox" id="'.htmlspecialchars($this->_id.'_'.$r_cnt.'_'.$c_cnt).'" name="'.htmlspecialchars($this->_id.'_'.$r_cnt.'[]').'" value="'.$c_cnt.'" />';
						break;
				}
				$c_cnt++;
				$s_ret.='<td class="optionCell">'.$s_cellHTML.'</td>';
			}			
			$s_ret.='</tr>';
			$r_cnt++;
		}
		$s_ret.='</table>';
		return $s_ret;
	}
}
/**
 * Creates three combined form elements to allow users to enter a date using three dropdown lists.
 * @package FormItBuilder
 */
class FormItBuilder_elementDate extends FormItBuilder_element{
	private $_dateFormat;
	private $_yearStart;
	private $_yearEnd;
	private $_defaultVal0;
	private $_defaultVal1;
	private $_defaultVal2;
	
	/**
	 * FormItBuilder_elementDate
	 * 
	 * Creates three combined form elements to allow users to enter a date using three dropdown lists.
	 *
	 * @param type $id The id of the field (three modified ids that add _day, _month, _year to the end of this id will result.
	 * @param type $label The label of the data element.
	 * @param type $year_start The start year to show in the year dropdown (default=current year - 90).
	 * @param type $year_end  Then end year to show in dropdown (default=current year + 10).
	 * @param type $defaultValue Pass in a unix timestamp to set a default date.
	 */
	function __construct($id, $label, $format='dd/mm/yyyy', $year_start=NULL, $year_end=NULL, $defaultValue=NULL ){
		parent::__construct($id,$label);
		$this->setDateFormat($format);
		$this->setYearStart($year_start);
		$this->setYearEnd($year_end);

		$this->_defaultVal0 = date('jS',$defaultValue);
		$this->_defaultVal1 = date('F',$defaultValue);
		$this->_defaultVal2 = date('Y',$defaultValue);
	}
	/**
	 * setDateFormat($value)
	 * 
	 * Sets the order of the date select elements. Default dd/mm/yyyy but can be any combination.
	 * @param string $value 
	 */
	public function setDateFormat($value) {
		$a_dateBits=explode('/',trim($value));
		
		$day = array_search('dd', $a_dateBits);
		$month = array_search('mm', $a_dateBits);
		$year = array_search('yyyy', $a_dateBits);
		
		$ar1 = array($day,$month,$year);
		$ar2 = array('day','month','year');
		array_multisort($ar1, $ar2);
		
		if(strlen($value)!==10 || $day===false || $month===false || $year===false){
			FormItBuilder::throwError('[Element: '.$this->_id.'] Date format "'.$value.'" is not valid.');
		}else{
			$this->_dateFormat=$ar2;
		}
	}
	/**
	 * Sets the year start for the year portion of date elements.
	 * @param int $value The start year (default=current year - 90)
	 */
	public function setYearStart($value){
		if($value===NULL){
			$this->_yearStart=date('Y')-90;
		}else{
			$this->_yearStart=$value;
		}		
	}
	/**
	 * Sets the year end for the year portion of date elements.
	 * @param int $value The end year (default=current year + 10).
	 */
	public function setYearEnd($value){
		if($value===NULL){
			$this->_yearEnd=date('Y')+10;
		}else{
			$this->_yearEnd=$value;
		}	
	}
	/**
	 * Gets the year start.
	 * @return int
	 */
	public function getYearStart(){return $this->_yearStart;}
	/**
	 * Gets the year end.
	 * @return int 
	 */
	public function getYearEnd($value){return $this->_yearEnd;}
	
	/**
	 * getDateFormat()
	 * 
	 * Returns the date format used by a field with a date FormRule.
	 * @return string
	 */
	public function getDateFormat() { return $this->_dateFormat; }
	
	/**
	 * outputHTML()
	 * 
	 * Outputs the HTML for the element.
	 * @return string 
	 */
	public function outputHTML(){
		//day options
		$a_days=array(''=>'');
		for($a=1;$a<32;$a++){
			$ordinalSuffix = date('S',strtotime('2000-01-'.$a));
			$a_days[$a.$ordinalSuffix]=$a.$ordinalSuffix;
		}
		//month options
		$s_monthStr=',January,February,March,April,May,June,July,August,September,October,November,December';
		$a_temp=explode(',',$s_monthStr);
		$a_months=array(''=>'');
		foreach($a_temp as $opt){
			$a_months[$opt]=$opt;
		}
		//year options
		$a_years=array();
		if($this->_yearStart>$this->_yearEnd){
			FormItBuilder::throwError('[Element: '.$this->_id.'] Date start "'.$this->_yearStart.'" is greater than the end year "'.$this->_yearEnd.'".');
		}
		for($a=$this->_yearStart;$a<$this->_yearEnd+1;$a++){
			$a_years[' '.$a]=$a; //blank space here to be a number instead of an index
		}
		$a_years['']='';
		$a_years=array_reverse($a_years);
		$cnt=0;
		foreach($this->_dateFormat as $datePart){
			$default = NULL;
			if($datePart=='day'){ $selectArray=$a_days;	$default=$this->_defaultVal0; }
			if($datePart=='month'){ $selectArray=$a_months; $default=$this->_defaultVal1; }
			if($datePart=='year'){ $selectArray=$a_years; $default=$this->_defaultVal2; }
			$drop = new FormItBuilder_elementSelect($this->_id.'_'.$cnt, '', $selectArray, $default);
			$s_ret.='<span class="elementDate_'.$cnt.'">'.$drop->outputHTML().'</span>';
			$cnt++;
		}
		
		return $s_ret;
	}
}

/**
 * Creates a text area element.
 * @package FormItBuilder
 */
class FormItBuilder_elementTextArea extends FormItBuilder_element{
	/**
	 * @ignore 
	 */
	private $_defaultVal;
	/**
	 * @ignore 
	 */
	private $_rows;
	/**
	 * @ignore 
	 */
	private $_cols;

	/**
	 * FormItBuilder_elementTextArea
	 * 
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
	/**
	 * outputHTML()
	 * 
	 * Outputs the HTML for the element.
	 * @return string 
	 */
	public function outputHTML(){
		//hidden field with same name is so we get a post value regardless of tick status
		if(isset($_POST[$this->_id])===true){
			$selectedStr=htmlspecialchars($_POST[$this->_id]);
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

/**
 * Creates a checkbox form element.
 * @package FormItBuilder
 */
class FormItBuilder_elementCheckbox extends FormItBuilder_element{
	/**
	 * @ignore 
	 */
	private $_value;
	/**
	 * @ignore 
	 */
	private $_uncheckedValue;
	/**
	 * @ignore 
	 */
	private $_checked;
	/**
	 * FormItBuilder_elementCheckbox
	 * 
	 * Creates a checkbox form element.
	 *
	 * @param string $id ID of checkbox
	 * @param string $label Label of checkbox
	 * @param string $value Value to show if user selects the checkbox
	 * @param boolean $uncheckedValue Value to show if user does not check the checkbox
	 * @param mixed $checked The checkbox will be checked by default if true is supplied OR if the checked value is supplied. e.g. if checked value is "Agree" and this parameter is set to "Agree" the checkbox will be checked by default.
	 */
	function __construct( $id, $label, $value='Checked', $uncheckedValue='Unchecked', $checked=false) {
		parent::__construct($id,$label);
		$this->_value=$value;
		$this->_checked=$checked;
		$this->_uncheckedValue=$uncheckedValue;
	}
	/**
	 * outputHTML()
	 * 
	 * Outputs the HTML for the element.
	 * @return string 
	 */
	public function outputHTML(){
		$a_uncheckedVal = $this->_uncheckedValue;
		if($this->_required===true){
			$a_uncheckedVal=''; // we do this because FormIt will not validate it as empty if unchecked value has a value.
		}
		if(isset($_POST[$this->_id])===true){
			if($_POST[$this->_id]==$this->_value){
				$selectedStr=' checked="checked"';
			}
		}else{
			if($this->_checked===true || $this->_value==$this->_checked){
				$selectedStr=' checked="checked"';
			}
		}
		//hidden field with same name is so we get a post value regardless of tick status
		$s_ret='<input type="hidden" name="'.htmlspecialchars($this->_id).'" value="'.htmlspecialchars($a_uncheckedVal).'" />'
		.'<input type="checkbox" id="'.htmlspecialchars($this->_id).'" name="'.htmlspecialchars($this->_id).'" value="'.htmlspecialchars($this->_value).'"'.$selectedStr.' />';
		return $s_ret;
	}
}

/**
 * Creates a group of checkbox elements.
 * @package FormItBuilder
 */
class FormItBuilder_elementCheckboxGroup extends FormItBuilder_element{
	/**
	 * @ignore 
	 */
	private $_values;
	/**
	 * @ignore 
	 */
	private $_showIndividualLabels;
	/**
	 * @ignore 
	 */
	private $_uncheckedValue;
	/**
	 * @ignore 
	 */
	private $_maxLength;
	/**
	 * @ignore 
	 */
	private $_minLength;
	
	
	/**
	 * FormItBuilder_elementCheckboxGroup
	 * 
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
	
	/**
	 * showIndividualLabels($value) / showIndividualLabels()
	 * 
	 * By default (true) each checkbox will have its own label. Users may wish to have checkboxes in some kind of table with custom surrounding HTML. In this case labels can be hidden.  If no value passed the method will return the current label display status. 
	 * @param boolean $value
	 * @return boolean 
	 */
	public function showIndividualLabels($value){
		if(func_num_args() == 0) {
			return $this->_showIndividualLabels;
		}else{
			$this->_showIndividualLabels = FormItBuilder::forceBool(func_get_arg(0));
		}
	}
	
	/**
	 * setMinLength($value)
	 * 
	 * Sets the minimum number of checkboxes that must be selected before the checkbox group will be valid (e.g. please select at least two options...).
	 * @param int $value 
	 */
	public function setMinLength($value) {
		$value = FormItBuilder::forceNumber($value);
		if($this->_maxLength!==NULL && $this->_maxLength<$value){
			FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set minimum length to "'.$value.'" when maximum length is "'.$this->_maxLength.'"');
		}else{
			//if($this->_required===false){
				//FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set minimum length to "'.$value.'" when field is not required.');
			//}else{
				$this->_minLength = FormItBuilder::forceNumber($value);
			//}
		}
	}
	/**
	 * setMaxLength($value)
	 * 
	 * Sets the maximum number of checkboxes that can be selected before the checkbox group will be valid (e.g. please select up to three options...).
	 * @param int $value 
	 */
	public function setMaxLength($value) {
		$value = FormItBuilder::forceNumber($value);
		if($this->_minLength!==NULL && $this->_minLength>$value){
			throw FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set maximum length to "'.$value.'" when minimum length is "'.$this->_minLength.'"');
		}else{
			$this->_maxLength = FormItBuilder::forceNumber($value);
		}
	}
	/**
	 * outputHTML()
	 * 
	 * Outputs the HTML for the element.
	 * @return string 
	 */
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
			// changed input type to checkbox
			// added [] to name
			$s_ret.='<div class="checkboxEl"><input type="checkbox" id="'.htmlspecialchars($this->_id.'_'.$i).'" name="'.htmlspecialchars($this->_name).'" value="'.htmlspecialchars($value['title']).'"';
			$selectedStr='';
			if(isset($_POST[$this->_id])===true){
				if(in_array($value['title'],$_POST[$this->_id])===true){
					$selectedStr=' checked="checked"';
				}
			}else{
				if(isset($value['checked'])===true && $value['checked']===true){
					$selectedStr=' checked="checked"';
				}
			}
			$s_ret.=$selectedStr.' /></div>';
			if($this->_showIndividualLabels===true){
				$s_ret.='<label for="'.htmlspecialchars($this->_id.'_'.$i).'">'.htmlspecialchars($value['title']).'</label>';
			}
			$s_ret.='</div>'."\r\n";
			$i++;
		}
		$s_ret.='</div>';
		return $s_ret;
	}
}

/**
 * Creates a text field form element.
 * @package FormItBuilder
 */
class FormItBuilder_elementText extends FormItBuilder_element{
	/**
	 * @ignore
	 */
	protected $_fieldType;
	/**
	 * @ignore
	 */
	protected $_maxLength;
	/**
	 * @ignore
	 */
	protected $_minLength;
	/**
	 * @ignore
	 */
	protected $_maxValue;
	/**
	 * @ignore
	 */
	protected $_minValue;
	/**
	 * @ignore
	 */
	protected $_dateFormat;
	/**
	 * @ignore
	 */
	protected $_defaultVal;

	/**
	 * FormItBuilder_elementText
	 * 
	 * Creates a text field.
	 * @param string $id The ID of the text field
	 * @param string $label The label of the text field
	 * @param string $defaultValue The default text to be written into the text field
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
	/**
	 * getMaxLength()
	 * 
	 * Returns the maximum string length for the field.
	 * @return int
	 */
	public function getMaxLength() { return $this->_maxLength; }
	/**
	 * getMinLength()
	 * 
	 * Returns the minimum string length for the field.
	 * @return int
	 */
	public function getMinLength() { return $this->_minLength; }
	/**
	 * getMaxValue()
	 * 
	 * Returns the maximum value for the field.
	 * @return int
	 */
	public function getMaxValue() { return $this->_maxValue; }
	/**
	 * getMinValue()
	 * 
	 * Returns the minimum string length for the field.
	 * @return int
	 */
	public function getMinValue() { return $this->_minValue; }
	/**
	 * getDateFormat()
	 * 
	 * Returns the date format used by a field with a date FormRule.
	 * @return string
	 */
	public function getDateFormat() { return $this->_dateFormat; }
	
	/**
	 * setMaxLength($value)
	 * 
	 * Sets the maximum string length for the field.
	 * This is generally done automatically via a FormRule.
	 * @param int $value 
	 */
	public function setMaxLength($value) {
		$value = FormItBuilder::forceNumber($value);
		if($this->_minLength!==NULL && $this->_minLength>$value){
			throw FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set maximum length to "'.$value.'" when minimum length is "'.$this->_minLength.'"');
		}else{
			$this->_maxLength = FormItBuilder::forceNumber($value);
		}
	}
	/**
	 * setMinLength($value)
	 * 
	 * Sets the minimum string length for the field.
	 * This is generally done automatically via a FormRule.
	 * @param int $value 
	 */
	public function setMinLength($value) {
		$value = FormItBuilder::forceNumber($value);
		if($this->_maxLength!==NULL && $this->_maxLength<$value){
			FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set minimum length to "'.$value.'" when maximum length is "'.$this->_maxLength.'"');
		}else{
			//if($this->_required===false){
				//FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set minimum length to "'.$value.'" when field is not required.');
			//}else{
				$this->_minLength = FormItBuilder::forceNumber($value);
			//}
		}
	}
	/**
	 * setMaxValue($value)
	 * 
	 * Sets the maximum numeric value for a field.
	 * This is generally done automatically via a FormRule.
	 * @param int $value 
	 */
	public function setMaxValue($value) {
		$value = FormItBuilder::forceNumber($value);
		if($this->_minValue!==NULL && $this->_minValue>$value){
			FormItBuilder::throwError('Cannot set maximum value to "'.$value.'" when minimum value is "'.$this->_minValue.'"');
		}else{
			$this->_maxValue = FormItBuilder::forceNumber($value);
		}
	}
	/**
	 * setMinValue($value)
	 * 
	 * Sets the minimum numeric value for a field.
	 * This is generally done automatically via a FormRule.
	 * @param int $value 
	 */
	public function setMinValue($value) {
		$value = FormItBuilder::forceNumber($value);
		if($this->_maxValue!==NULL && $this->_maxValue<$value){
			FormItBuilder::throwError('[Element: '.$this->_id.'] Cannot set minimum value to "'.$value.'" when maximum value is "'.$this->_maxValue.'"');
		}else{
			$this->_minValue = FormItBuilder::forceNumber($value);
		}
	}
	/**
	 * setDateFormat($value)
	 * 
	 * Sets the date format used by a field with a date FormRule.
	 * This is generally done automatically via a FormRule.
	 * @param string $value 
	 */
	public function setDateFormat($value) {
		$value=trim($value);
		if(empty($value)===true){
			FormItBuilder::throwError('[Element: '.$this->_id.'] Date format is not valid.');
		}else{
			$this->_dateFormat=$value;
		}
	}
	/**
	 * outputHTML()
	 * 
	 * Outputs the HTML for the element.
	 * @return string 
	 */
	public function outputHTML(){
		$a_classes=array();
		
		//hidden field with same name is so we get a post value regardless of tick status
		if(isset($_POST[$this->_id])===true){
			$selectedStr=$_POST[$this->_id];
		}else{
			$selectedStr=$this->_defaultVal;
		}
		
		$s_ret='<input type="'.$this->_fieldType.'" name="'.htmlspecialchars($this->_id).'" id="'.htmlspecialchars($this->_id).'"'.($this->_fieldType=='file'?'':' value="'.htmlspecialchars($selectedStr).'"');
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

/**
 * Creates a password field element.
 * @package FormItBuilder
 */
class FormItBuilder_elementPassword extends FormItBuilder_elementText{
	/**
	 * FormItBuilder_elementPassword
	 * 
	 * Creates a password field.
	 * @param string $id The ID of the password field
	 * @param string $label The label of the password field
	 * @param string $defaultValue The default text to be written into the password field
	 */
	function __construct( $id, $label, $defaultValue=NULL ) {
		parent::__construct($id,$label,$defaultValue);
		$this->_fieldType='password';
	}
}

/**
 * Creates a hidden field form element.
 * @package FormItBuilder
 */
class FormItBuilder_elementHidden extends FormItBuilder_elementText{
	/**
	 * FormItBuilder_elementHidden
	 * 
	 * Creates a hidden field.
	 * @param string $id The ID of the hidden field
	 * @param string $label The label of the hidden field
	 * @param string $defaultValue The default value to be written into the hidden field
	 */
	function __construct( $id, $label, $defaultValue=NULL ) {
		parent::__construct($id,$label,$defaultValue);
		$this->_fieldType='hidden';
		$this->_showInEmail=false;
	}
}
/**
 * FormItBuilder_elementFile
 * 
 * Creates a file field form element.
 * @package FormItBuilder
 */
class FormItBuilder_elementFile extends FormItBuilder_elementText{
	/**
	 * Creates a file field element allowing upload of file to the server (and attached to email)
	 * @param string $id The ID of the file element
	 * @param string $label The label of the file element
	 */
	function __construct( $id, $label ) {
		parent::__construct($id,$label);
		$this->_fieldType='file';
		$this->_showInEmail=false;
	}
}

?>