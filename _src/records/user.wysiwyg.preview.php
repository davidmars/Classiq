<?php
/**
 * @var User $vv
 */

use Classiq\Models\User;

?>
<div class="preview-record" <?=$view->attrRefresh($vv->uid())?>>
    <?if($vv->id):?>
        <span class="icon"><?=pov()->svg->use($vv->roleSvg())?></span>
        <?=$view->render("./tip-errors")?>
        <div>
            <div class="title" title="<?=$vv->name?>"><?=$vv->name?></div>
            <div class="type"><span class="cq-fg-selected"><?=$vv->role?$vv->role:"Nouveau compte"?></span></div>
        </div>
    <?else:?>
        ...
    <?endif?>
</div>