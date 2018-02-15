<?php

use Classiq\Models\Page;
/** @var Page $record */
$record=$vv->box();

?>
<div <?=$view->attrRefresh($vv->uid())?> cq-on-model-saved="refresh(this)" class="record" record-type="<?=$record->modelType()?>">
    <div class=" cq-list-item two-lines" >

        <div class="text">
            <?=$record->views()->wysiwygPreview()->render()?>
        </div>

        <div class="end three-icons">

            <?//lock/delete--------------?>
            <?if($vv->conf_prevent_trash):?>
                <span class="cq-fg-danger"><?=pov()->svg->use("cq-lock")?></span>
            <?else:?>
                <button cq-on-click="db.trash(<?=$vv->uid()?>)" class="cq-unstyled cq-fg-disabled cq-fg-danger-hover">
                    <?=pov()->svg->use("cq-trash")?>
                </button>
            <?endif?>

            <?//ouvrir la page--------------?>
            <?if($record::$isPage):?>
                <a class="cq-fg-disabled cq-fg-1-hover cq-unstyled"
                   title="Aller sur la page" href="<?=$record->href()?>"
                   cq-on-click="ui.bigMenu.close()" ><?=pov()->svg->use("cq-forward")?></a>
            <?else:?>
                <span></span>
            <?endif?>

            <?//éditer dans l'admin--------------?>
            <button  class="cq-fg-disabled cq-fg-1-hover cq-unstyled"
                     title="Éditer"
                     cq-on-click="editRecord(<?=$record->uid()?>)">
                <?=pov()->svg->use("cq-edit")?>
            </button>
        </div>
    </div>
</div>
