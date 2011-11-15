<?php
require_once $modx->getOption('core_path',null,MODX_CORE_PATH).'components/formitbuilder/model/formitbuilder/FormItBuilder.class.php';
$o_form = $GLOBALS['FormItBuilder_hookCommands']['formObj'];
$a_commands = $GLOBALS['FormItBuilder_hookCommands']['commands'];
return $o_form->processHooks($a_commands);
?>
