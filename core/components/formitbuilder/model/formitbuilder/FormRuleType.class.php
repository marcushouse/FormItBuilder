<?php
/**
 * Contains RuleType constants.
 * @package FormItBuilder
 */

/**
 * FormRuleType
 * 
 * A simple class to define strings as constants for rule types to make them more easily read.
 * @package FormItBuilder
 */
class FormRuleType extends FormItBuilderCore{
	const fieldMatch = 'fieldMath';
	const required = 'required';
	const email = 'email';
	const numeric = 'numeric';
	const minimumLength = 'minimumLength';
	const maximumLength = 'maximumLength';
	const minimumValue = 'minimumValue';
	const maximumValue = 'maximumValue';
	const file = 'file';
	const date = 'date';
}

?>
