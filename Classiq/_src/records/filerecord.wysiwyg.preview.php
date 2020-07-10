<?php
/**
 * @var Filerecord $vv
 */

use Classiq\Models\Classiqmodel;
use Classiq\Models\Filerecord;

?>
<div class="preview-record" <?php echo $view->attrRefresh($vv->uid())?> >
    <?php if($vv->id):?>

        <?php if($vv->isImage()):?>
            <span class="icon image big">
                <img alt="" src="<?php echo $vv->httpPath()?>">
            </span>
        <?php elseif($vv->isVideo()):?>
            <span class="icon"><?php echo pov()->svg->use("cq-circle-play")?></span>
        <?php else: ?>
            <span class="icon"><?php echo pov()->svg->use("cq-file")?></span>
        <?php endif; ?>

        <i cq-tip class="cq-th-danger inline" data-count="0"></i>

        <div>

            <div class="title" title="<?php echo $vv->name?>">
                <?php echo $vv->name?>
            </div>

            <div class="type">
                <?php echo $vv->mime?>
                <?php if($vv->isImage()):?>
                    <?php echo $vv->image_width?>x<?php echo $vv->image_height?>px
                <?php endif; ?>
                <?php echo $vv->humanFileSize()?>
            </div>
        </div>
    <?php else: ?>
        ...
    <?php endif; ?>
</div>