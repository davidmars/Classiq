<?php

use Classiq\Models\Classiqmodel;

$uid=the()->request("uid");
$page=Classiqmodel::getByUid($uid);

/** @var Classiqmodel $vv */
?>
<?if(cq()->wysiwyg() && $page):?>
<div>
    <?=$page->views()->wysiwygBoxes()?>
</div>

<?endif;?>
