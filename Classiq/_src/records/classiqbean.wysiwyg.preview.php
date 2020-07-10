<?php
/**
 * @var \Classiq\Models\Classiqbean $vv
 */

?>
<div class="preview-record" <?php echo $view->attrRefresh($vv->uid())?>>
    <?php if($vv->id):?>
        <span class="icon">
            <?php echo pov()->svg->use($vv::$icon)?>
        </span>
        <i>&nbsp;</i>
        <div>
            <div class="title"><?php echo $vv->uid()?> </div>
            <span class="type">
                <?php echo $vv->modelType()?>@<?php echo $vv->id?>
            </span>

        </div>
    <?php else: ?>
        ...
    <?php endif; ?>
</div>