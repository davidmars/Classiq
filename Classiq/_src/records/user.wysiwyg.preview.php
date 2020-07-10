<?php
/**
 * @var User $vv
 */

use Classiq\Models\User;

?>
<div class="preview-record" <?php echo $view->attrRefresh($vv->uid())?>>
    <?php if($vv->id):?>
        <span class="icon"><?php echo pov()->svg->use($vv->roleSvg())?></span>
        <?php echo $view->render("./tip-errors")?>
        <div>
            <div class="title" title="<?php echo $vv->name?>"><?php echo $vv->name?></div>
            <div class="type"><span class="cq-fg-selected"><?php echo $vv->role?$vv->role:"Nouveau compte"?></span></div>
        </div>
    <?php else: ?>
        ...
    <?php endif; ?>
</div>