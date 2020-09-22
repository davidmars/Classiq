<?php
/** @var \Classiq\Models\Classiqmodel $vv */
$errs=$vv->getErrors()
?>

<div class="cq-box cq-unmaterialized">
    <div text-right="">
        <time><?php echo date("H:i:s")?></time>
    </div>
    <div class="cq-list-item two-lines">
        <div class="text">
            <?php echo $vv->views()->wysiwygPreview()->render()?>
        </div>
        <div class="end two-icons">
            <?php if($vv->conf_prevent_trash):?>
                <span class="cq-fg-danger"><?php echo pov()->svg->use("cq-lock")?></span>
            <?php else: ?>
                <button cq-on-click="db.trash(<?php echo $vv->uid()?>)" class="cq-unstyled cq-fg-disabled cq-fg-danger-hover">
                    <?php echo pov()->svg->use("cq-trash")?>
                </button>
            <?php endif; ?>

        </div>
    </div>

    <fieldset cq-display-if="dev">
        <?php echo $vv->wysiwyg()->field("conf_prevent_trash")
            ->string()
            ->select(
                    [
                        cq()->tradWysiwyg("Protégé (ne peut être effacé)")=>"1",
                        cq()->tradWysiwyg("Peut être effacé")=>0
                    ]
            )?>
    </fieldset>

</div>

<?php if($errs):?>
    <div class="cq-box cq-th-danger">
        <?php foreach ($errs as $field=>$message):?>
            <div><?php echo $message?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


<div class="cq-box wysiwyg-config-classiqmodel">
    <label><?php echo cq()->tradWysiwyg("Titre / Nom")?></label>
    <?php echo $vv->wysiwyg()->field("name")
        ->string()
        ->input("text",cq()->tradWysiwyg("Titre de la page..."))
    ?>
    <?php foreach (the()->project->languages as $lang):?>
        <?php echo $vv->wysiwyg()->field("name_$lang")
            ->string()
            ->input("text",$lang)
            ->setAttribute("data-lang",$lang)
        ?>
    <?php endforeach; ?>
</div>
