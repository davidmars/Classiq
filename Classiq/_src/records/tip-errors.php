<?php
/**
 * Le tip avec les erreurs
 */

/** @var Classiq\Models\Classiqmodel $vv  */
?>
<i cq-tip="" class="cq-th-danger inline" data-count="<?php echo count($vv->getErrors())?>" title="<?php echo $vv->getErrorsString()?>"></i>