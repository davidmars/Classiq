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
                <img alt="" src="<?=$vv->httpPath()?>">
            </span>
        <?elseif($vv->isVideo()):?>
            <span class="icon"><?=pov()->svg->use("cq-circle-play")?></span>
        <?else:?>
            <span class="icon"><?=pov()->svg->use("cq-file")?></span>
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
        </div>
    <?else:?>
        ...
    <?endif?>
</div>