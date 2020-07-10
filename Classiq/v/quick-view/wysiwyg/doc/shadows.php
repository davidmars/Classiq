<div class="cq-cols">
    <?php foreach (["","-2","-3"] as $suf): $class="wysiwyg-shadow$suf"; ?>
    <div class="wysiwyg-pad-xy">
        <div class="wysiwyg-pad-xy cq-th-white <?php echo $class?>">
            <h4>.<?php echo $class?></h4>
            <code><?php echo $class?></code><br>
            <hr>
            <hr>
            <hr>
            <hr>
        </div>
    </div>
    <?php endforeach; ?>
</div>
