<?php
/**
 * @var Page $vv
 */

use Classiq\Models\Classiqmodel;
use Classiq\Models\Page;

?>
<div class="preview-record" <?php echo $view->attrRefresh($vv->uid())?>>
    <?php if($vv->id):?>

        <span class="icon image">
            <i style="background-image: url('<?php echo $vv->thumbnail()->sizeMax(200,200)->bgColor("EEEEEE")->jpg()->href()?>')"></i>
            <?php echo pov()->svg->use($vv::$icon)?>
        </span>
        <?php echo $view->render("./tip-errors")?>
        <div>
            <div class="title" title="<?php echo $vv->name?>"><?php echo $vv->name?></div>
            <a target="_blank" href="<?php echo $vv->href()?>" class="type"><?php echo $vv->modelType()?>@<?php echo $vv->id?></a>

        </div>



    <?php else: ?>
        ...
    <?php endif; ?>
</div>