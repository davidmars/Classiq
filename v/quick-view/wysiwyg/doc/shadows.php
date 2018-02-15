<div class="cq-cols">
    <?foreach (["","-2","-3"] as $suf): $class="wysiwyg-shadow$suf"; ?>
    <div class="wysiwyg-pad-xy">
        <div class="wysiwyg-pad-xy cq-th-white <?=$class?>">
            <h4>.<?=$class?></h4>
            <code><?=$class?></code><br>
            <hr>
            <hr>
            <hr>
            <hr>
        </div>
    </div>
    <? endforeach; ?>
</div>
