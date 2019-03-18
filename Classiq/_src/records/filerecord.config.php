<?php
/** @var \Classiq\Models\Filerecord $vv */
$errs=$vv->getErrors();
$vv->localPath()
?>


<div class="cq-box ">

    <label><?=cq()->tradWysiwyg("Fichier")?></label>
    <a href="<?=$vv->httpPath()?>" target="_blank">Télécharger</a>

    <label><?=cq()->tradWysiwyg("Type")?></label>
    <?=$vv->mime?><br>

    <label><?=cq()->tradWysiwyg("Taille")?></label>
    <?=$vv->humanFileSize()?><br>

    <?if($vv->isImage()):?>
        <label><?=cq()->tradWysiwyg("Dimensions")?></label>
        <?=$vv->image_width?>x<?=$vv->image_height?>px <br><br>
        <div style="background-image: var(--cq-img-toshop-grid); padding-bottom: 100%; position: relative; max-width: <?=$vv->image_width?>px; max-height: <?=$vv->image_height?>px;margin-bottom:20px;">
            <div style="background-position:center;background-repeat:no-repeat;background-size:contain;position:absolute;background-image: url('<?=$vv->httpPath()?>');height: 100%;width:100%;"></div>
        </div>
    <?endif?>

    <fieldset cq-display-if="dev">
        <label><?=cq()->tradWysiwyg("Chemin vers le fichier")?> (<?=cq()->tradWysiwyg("Relatif à")?> <code style="text-transform: none"><?=the()->fileSystem->uploadsPath?>)</code></label>
        <?=$vv->wysiwyg()->field("path")
            ->string()
            ->input("text",cq()->tradWysiwyg("ne pas se tromper")." :)")
        ?>
    </fieldset>

</div>

