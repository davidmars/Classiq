<?php

use Classiq\Models\Classiqmodel;

$uid=the()->request("uid");
$page=Classiqmodel::getByUid($uid);

/** @var Classiqmodel $vv */
?>
<?php if(cq()->wysiwyg() && $page):?>
<div>
    <?php echo $page->views()->wysiwygBoxes()?>
</div>

<?php endif; ?>
