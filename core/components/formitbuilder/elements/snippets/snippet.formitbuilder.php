<?php
// Make a snippet called 'FormIt_formBuilder' and place this content in it.
require_once $modx->getOption('core_path',null,MODX_CORE_PATH).'components/formitbuilder/model/formitbuilder/FormItBuilder.class.php';
$o_formBuilder = new FormIt_FormBuilder($modx,$scriptProperties);

return $o_formBuilder->output();
?>