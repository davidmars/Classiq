<?php
$colors=["rien-du-tout","white","grey-light","grey-dark","black","danger"]
?>

<div cq-box class="th-danger">
    Attention, il ne faut pas imbriquer un thème dans un autre
</div>
<div class="cq-cols">
<?foreach ($colors as $color): $class="wysiwyg-th-$color";?>
<div class="wysiwyg-pad-xy">
    <div class="wysiwyg-pad-xy <?=$class?>">
        <h3><?=$class?></h3>
        <code><?=$class?></code><br>
        <p><?=pov()->utils->string->loremIspum(20)?></p>
        <hr>
        <p><?=pov()->utils->string->loremIspum(20)?></p>
    </div>
</div>
<? endforeach; ?>
</div>
