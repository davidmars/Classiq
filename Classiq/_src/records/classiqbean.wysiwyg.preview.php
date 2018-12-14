<?php
/**
 * @var \Classiq\Models\Classiqbean $vv
 */

?>
<div class="preview-record" <?=$view->attrRefresh($vv->uid())?>>
    <?if($vv->id):?>
        <span class="icon">
            <?=pov()->svg->use($vv::$icon)?>
        </span>
        <i>&nbsp;</i>
        <div>
            <div class="title"><?=$vv->uid()?> </div>
            <span class="type">
                <?=$vv->modelType()?>@<?=$vv->id?>
            </span>

        </div>
    <?else:?>
        ...
    <?endif?>
</div>