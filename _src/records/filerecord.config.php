<?php
/** @var \Classiq\Models\Filerecord $vv */
$errs=$vv->getErrors()
?>


<div class="cq-box ">


    <label>Type</label>
    <?=$vv->mime?><br>

    <label>Taille</label>
    <?=$vv->humanFileSize()?><br>


    <?if($vv->isImage()):?>
    <label>Dimentions</label>
    <?=$vv->image_width?>x<?=$vv->image_height?>px <br><br>
    <div style="background-image: var(--cq-img-toshop-grid); padding-bottom: 100%; position: relative; max-width: <?=$vv->image_width?>px; max-height: <?=$vv->image_height?>px">
        <div style="background-position:center;background-repeat:no-repeat;background-size:contain;position:absolute;background-image: url('<?=$vv->httpPath()?>');height: 100%;width:100%;"></div>
    </div>
    <?endif?>

</div>

