<?php
use Classiq\Models\Classiqmodel;

$types=the()->request("types");
$collections = [];
if($types){
    $types=explode(",",$types);
    foreach ($types as $type){
        $type=strtolower($type);
        $collections[$type]=db()->findAll($type,"ORDER BY name");
    }
}
?>
<div class="cq-box">
    <?foreach ($collections as $type=>$beans):?>
        <?foreach ($beans as $bean):?>
            <?php
            /** @var  Classiqmodel $record */
            $record=$bean->box();
            ?>
            <?=$view->render("./browse-records-line",$record)?>
        <?endforeach;?>
    <?endforeach;?>
</div>

