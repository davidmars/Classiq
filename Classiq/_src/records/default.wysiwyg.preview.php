<?php
/**
 * @var Classiqmodel $vv
 */

use Classiq\Models\ClassicModelSchema;
use Classiq\Models\Classiqmodel;

?>
<div class="preview-record" <?php echo $view->attrRefresh($vv->uid())?>>
    <?php if($vv->id):?>
        <span class="icon"><?php echo  pov()->svg->use(ClassicModelSchema::icon($vv))?></span>
        <?php echo $view->render("./tip-errors")?>
        <div>
            <div class="title" title="<?php echo $vv->name?>"><?php echo $vv->name?></div>
            <div class="type"><?php echo $vv->modelType()?>@<?php echo $vv->id?></div>
        </div>
    <?php else: ?>
        ...
    <?php endif; ?>
</div>