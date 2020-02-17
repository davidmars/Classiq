<?php
/**
 * @var \Classiq\Models\Classiqmodel $vv
 */

?>
<div class="preview-record" <?=$view->attrRefresh($vv->uid())?>>
    <?if($vv->id):?>
        <span class="icon">
            <?=pov()->svg->use($vv::$icon)?>
        </span>
        <?=$view->render("./tip-errors")?>
        <div>
            <div class="title"><?=$vv->name?> </div>
            <span class="type">
                <?=$vv->modelType()?>@<?=$vv->id?>
            </span>

        </div>
    <?else:?>
        ...
    <?endif?>
</div>