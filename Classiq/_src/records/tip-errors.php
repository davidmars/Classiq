<?php
/**
 * Le tip avec les erreurs
 */

/** @var Classiq\Models\Classiqmodel $vv  */
?>
<i cq-tip="" class="cq-th-danger inline" data-count="<?=count($vv->getErrors())?>" title="<?=$vv->getErrorsString()?>"></i>