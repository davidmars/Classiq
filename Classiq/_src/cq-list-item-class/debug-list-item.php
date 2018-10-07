<?php
/**
 *
 * @var Classiq\Models\JsonModels\ListItem $vv
 *
 *
 */
?>
<?if(Classiq\Wysiwyg\Wysiwyg::$enabled):?>
<div <?=$vv->wysiwyg()->attr()?> class="">
    <div id="cq-style" class="gr">
        <div text-center class="cq-box cq-th-danger">
            Ce block ne fonctionne pas car le template <code><?=$vv->data["path"]?></code> n'est pas/plus valide.
        </div>
    </div>
</div>
<?endif;?>
