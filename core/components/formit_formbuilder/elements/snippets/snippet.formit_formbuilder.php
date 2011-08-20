<?php
require_once $modx->getOption('core_path',null,MODX_CORE_PATH).'components/formit_formbuilder/model/formit/formit_formbuilder.class.php';
$o_formBuilder = new FormIt_FormBuilder($modx,$scriptProperties);

return $o_formBuilder->output();
?>