<?php
$colors=["rien-du-tout","white","grey-light","grey-dark","black","danger"]
?>
<div class="cq-cols">
    <?php foreach ($colors as $color): $class="wysiwyg-alert-$color";?>
        <div cq-box class="<?php echo $class?>">
            <h3><?php echo $class?></h3>
            <code>.<?php echo $class?></code>
            <p><?php echo pov()->utils->string->loremIspum(50)?></p>
        </div>
    <?php endforeach; ?>
</div>
