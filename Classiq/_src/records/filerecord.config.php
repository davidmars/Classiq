<?php
/** @var \Classiq\Models\Filerecord $vv */
$errs=$vv->getErrors();
$vv->localPath()
?>


<div class="cq-box ">

    <label><?php echo cq()->tradWysiwyg("Fichier")?></label>
    <a href="<?php echo $vv->httpPath()?>" target="_blank">Télécharger</a>

    <label><?php echo cq()->tradWysiwyg("Type")?></label>
    <?php echo $vv->mime?><br>

    <label><?php echo cq()->tradWysiwyg("Taille")?></label>
    <?php echo $vv->humanFileSize()?><br>

    <?php if($vv->isImage()):?>
        <label><?php echo cq()->tradWysiwyg("Dimensions")?></label>
        <?php echo $vv->image_width?>x<?php echo $vv->image_height?>px <br><br>
        <div style="background-image: var(--cq-img-toshop-grid); padding-bottom: 100%; position: relative; max-width: <?php echo $vv->image_width?>px; max-height: <?php echo $vv->image_height?>px;margin-bottom:20px;">
            <div style="background-position:center;background-repeat:no-repeat;background-size:contain;position:absolute;background-image: url('<?php echo $vv->httpPath()?>');height: 100%;width:100%;"></div>
        </div>
    <?php endif; ?>

    <fieldset cq-display-if="dev">
        <label><?php echo cq()->tradWysiwyg("Chemin vers le fichier")?> (<?php echo cq()->tradWysiwyg("Relatif à")?> <code style="text-transform: none"><?php echo the()->fileSystem->uploadsPath?>)</code></label>
        <?php echo $vv->wysiwyg()->field("path")
            ->string()
            ->input("text",cq()->tradWysiwyg("ne pas se tromper")." :)")
        ?>
    </fieldset>

</div>

