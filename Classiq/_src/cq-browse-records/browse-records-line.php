<?php

use Classiq\Models\Page;
/** @var Page $record */
$record=$vv->box();

?>
<div <?php echo $view->attrRefresh($vv->uid())?> cq-on-model-saved="refresh(this)" class="record" record-type="<?php echo $record->modelType()?>">
    <div class=" cq-list-item two-lines" >

        <div class="text">
            <?php echo $record->views()->wysiwygPreview()->render()?>
        </div>

        <div class="end three-icons">

            <?php //lock/delete--------------?>
            <?php if($vv->conf_prevent_trash):?>
                <span class="cq-fg-danger"><?php echo pov()->svg->use("cq-lock")?></span>
            <?php else: ?>
                <button cq-on-click="db.trash(<?php echo $vv->uid()?>)" class="cq-unstyled cq-fg-disabled cq-fg-danger-hover">
                    <?php echo pov()->svg->use("cq-trash")?>
                </button>
            <?php endif; ?>

            <?php //ouvrir la page--------------?>
            <?php if($record::$isPage):?>
                <a class="cq-fg-disabled cq-fg-1-hover cq-unstyled"
                   title="Aller sur la page" href="<?php echo $record->href()?>"
                   cq-on-click="ui.bigMenu.close()" ><?php echo pov()->svg->use("cq-forward")?></a>
            <?php else: ?>
                <span></span>
            <?php endif; ?>

            <?php //éditer dans l'admin--------------?>
            <button  class="cq-fg-disabled cq-fg-1-hover cq-unstyled"
                     title="Éditer"
                     cq-on-click="editRecord(<?php echo $record->uid()?>)">
                <?php echo pov()->svg->use("cq-edit")?>
            </button>
        </div>
    </div>
</div>
