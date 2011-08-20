<?php
class FormIt_FormBuilder{
	/**
	 * A reference to the modX instance
	 * @var modX $modx
	 */
	public $modx;
	/**
	 * A configuration array
	 * @var array $config
	 */
	public $config;

	/**
	 * FormIt constructor
	 *
	 * @param modX &$modx A reference to the modX instance.
	 * @param array $config An array of configuration options. Optional.
	 */
	function __construct(modX &$modx,array $config = array()) {
	    $this->modx = &$modx;
		$this->config = $config;
	}
	function output(){
		return 'TEST OUTPUT'.print_r($config,true);
	}
}
?>