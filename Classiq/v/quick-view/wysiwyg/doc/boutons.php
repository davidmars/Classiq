<?php
$colors=["rien-du-tout","white","grey-light","grey-dark","black","danger"]
?>
<div class="cq-cols">
    <div>
        <?foreach ($colors as $color): $class="cq-btn-$color";?>
            <div>
                <h3><code>.<?=$class?></code></h3>
                <button class="cq-btn <?=$class?>">Button</button>
                <input type="submit" value="submit" class="cq-btn <?=$class?>">
                <a href="#" class="cq-btn <?=$class?>">href</a>
                <div class="input-file-wrap cq-btn-file-wrap">
                    <button class="cq-btn <?=$class?>">Select File</button>
                    <input type="file">
                </div>
            </div>
        <?endforeach;?>
    </div>
    <div>
        <?foreach ($colors as $color): $class="cq-btn-$color";?>
            <div>
                <h3><code>.<?=$class?>  &.wysiwyg-loading </code></h3>
                <button class="cq-btn wysiwyg-loading <?=$class?>">Button</button>
                <input type="submit" value="submit" class="cq-btn  wysiwyg-loading <?=$class?>">
                <a href="#" class="cq-btn wysiwyg-loading  <?=$class?>">href</a>
                <div class="input-file-wrap cq-btn-file-wrap">
                    <button class="cq-btn wysiwyg-loading <?=$class?>">Select File</button>
                    <input type="file">
                </div>
            </div>
        <?endforeach;?>
    </div>
</div>
