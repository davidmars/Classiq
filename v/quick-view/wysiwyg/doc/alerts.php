<?php
$colors=["rien-du-tout","white","grey-light","grey-dark","black","danger"]
?>
<div class="cq-cols">
    <?foreach ($colors as $color): $class="wysiwyg-alert-$color";?>
        <div cq-box class="<?=$class?>">
            <h3><?=$class?></h3>
            <code>.<?=$class?></code>
            <p><?=pov()->utils->string->loremIspum(50)?></p>
        </div>
    <?endforeach;?>
</div>
