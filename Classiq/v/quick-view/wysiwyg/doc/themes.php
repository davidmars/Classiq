<?php
$colors=["rien-du-tout","white","grey-light","grey-dark","black","danger"]
?>

<div cq-box class="th-danger">
    Attention, il ne faut pas imbriquer un thème dans un autre
</div>
<div class="cq-cols">
<?php foreach ($colors as $color): $class="wysiwyg-th-$color";?>
<div class="wysiwyg-pad-xy">
    <div class="wysiwyg-pad-xy <?php echo $class?>">
        <h3><?php echo $class?></h3>
        <code><?php echo $class?></code><br>
        <p><?php echo pov()->utils->string->loremIspum(20)?></p>
        <hr>
        <p><?php echo pov()->utils->string->loremIspum(20)?></p>
    </div>
</div>
<?php endforeach; ?>
</div>
