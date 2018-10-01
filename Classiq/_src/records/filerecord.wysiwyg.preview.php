<?php
/**
 * @var Filerecord $vv
 */

use Classiq\Models\Classiqmodel;
use Classiq\Models\Filerecord;

?>
<div class="preview-record" <?=$view->attrRefresh($vv->uid())?> >
    <?if($vv->id):?>

        <?if($vv->isImage()):?>
            <span class="icon image big">
                <i style="background-image: url('<?=$vv->httpPath()?>')"></i>
            </span>
        <?elseif($vv->isVideo()):?>
            <span class="icon"><?=cq()->icoWysiwyg("circle-play")?></span>
        <?else:?>
            <span class="icon"><?=cq()->icoWysiwyg("file")?></span>
        <?endif?>

        <i cq-tip class="cq-th-danger inline" data-count="0"></i>

        <div>

            <div class="title" title="<?=$vv->name?>">
                <?=$vv->name?>
            </div>

            <div class="type">
                <?=$vv->mime?>
                <?if($vv->isImage()):?>
                    <?=$vv->image_width?>x<?=$vv->image_height?>px
                <?endif?>
                <?=$vv->humanFileSize()?>
            </div>
            <!--<div class="preview-type"><?=$vv->modelType()?>@<?=$vv->id?> </div>-->
        </div>
    <?else:?>
        ...
    <?endif?>
</div>