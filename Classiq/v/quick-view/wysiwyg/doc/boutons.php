<?php
$colors=["rien-du-tout","white","grey-light","grey-dark","black","danger"]
?>
<div class="cq-cols">
    <div>
        <?php foreach ($colors as $color): $class="cq-btn-$color";?>
            <div>
                <h3><code>.<?php echo $class?></code></h3>
                <button class="cq-btn <?php echo $class?>">Button</button>
                <input type="submit" value="submit" class="cq-btn <?php echo $class?>">
                <a href="#" class="cq-btn <?php echo $class?>">href</a>
                <div class="input-file-wrap cq-btn-file-wrap">
                    <button class="cq-btn <?php echo $class?>">Select File</button>
                    <input type="file">
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div>
        <?php foreach ($colors as $color): $class="cq-btn-$color";?>
            <div>
                <h3><code>.<?php echo $class?>  &.wysiwyg-loading </code></h3>
                <button class="cq-btn wysiwyg-loading <?php echo $class?>">Button</button>
                <input type="submit" value="submit" class="cq-btn  wysiwyg-loading <?php echo $class?>">
                <a href="#" class="cq-btn wysiwyg-loading  <?php echo $class?>">href</a>
                <div class="input-file-wrap cq-btn-file-wrap">
                    <button class="cq-btn wysiwyg-loading <?php echo $class?>">Select File</button>
                    <input type="file">
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
