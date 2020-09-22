<?php
/**
 * @var \Classiq\Models\Classiqmodel $vv
 */

?>
<div class="preview-record" <?php echo $view->attrRefresh($vv->uid())?>>
    <?php if($vv->id):?>
        <span class="icon">
            <?php echo pov()->svg->use($vv::$icon)?>
        </span>
        <?php echo $view->render("./tip-errors")?>
        <div>
            <div class="title"><?php echo $vv->name?> </div>
            <span class="type">
                <?php echo $vv->modelType()?>@<?php echo $vv->id?>
            </span>

        </div>
    <?php else: ?>
        ...
    <?php endif; ?>
</div>