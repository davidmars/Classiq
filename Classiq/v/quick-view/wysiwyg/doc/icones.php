<?php
$iconsList=file_get_contents("dist/svg-collection/cq.json");
$iconsList=json_decode($iconsList);
$iconsList=$iconsList->symbols;

?>
<div class="cq-cols">
    <?php foreach ($iconsList as $name):?>

        <div text-center class="cq-col-2">
            <div cq-box>
                <?php echo pov()->svg->use($name)->addClass("wysiwyg-icon")?>
                <pre><?php echo $name?></pre>
            </div>
        </div>
    <?php endforeach; ?>
</div>