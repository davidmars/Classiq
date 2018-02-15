<?php
/** @var \Classiq\Models\Classiqmodel $vv */
$errs=$vv->getErrors()
?>

<div class="cq-box cq-unmaterialized">
    <div text-right="">
        <time><?=date("H:i:s")?></time>
    </div>
    <div class="cq-list-item two-lines">
        <div class="text">
            <?=$vv->views()->wysiwygPreview()->render()?>
        </div>
        <div class="end two-icons">
            <?if($vv->conf_prevent_trash):?>
                <span class="cq-fg-danger"><?=pov()->svg->use("cq-lock")?></span>
            <?else:?>
                <button cq-on-click="db.trash(<?=$vv->uid()?>)" class="cq-unstyled cq-fg-disabled cq-fg-danger-hover">
                    <?=pov()->svg->use("cq-trash")?>
                </button>
            <?endif?>

        </div>
    </div>

    <fieldset cq-display-if="dev">
        <?=$vv->wysiwyg()->field("conf_prevent_trash")
            ->string()
            ->select(["Protégé (ne peut être effacé)"=>"1","Peut être effacé"=>0])?>
    </fieldset>

</div>

<?if($errs):?>
    <div class="cq-box cq-th-danger">
        <?foreach ($errs as $field=>$message):?>
            <div><?=$message?></div>
        <?endforeach?>
    </div>
<?endif?>


<div class="cq-box">
    <label>Titre / Nom</label>
    <?=$vv->wysiwyg()->field("name")
        ->string()
        ->input("text","Titre de la page...")
    ?>
</div>
