<?php
use Classiq\Models\Classiqmodel;

$types=the()->request("types");
$collections = [];

$total=0;
$start=the()->request("start",0);
if($types){
    $types=explode(",",$types);
    foreach ($types as $type){
        $type=strtolower($type);
        $collections[$type]=db()->findAll($type,"ORDER BY name");
    }
}


$list=[];
foreach ($collections as $type=>$beans){
    foreach ($beans as $bean){
        $list[]=$bean;
    }
}

$displayed=0;
?>
<div class="cq-box">
    <?foreach ($list as $bean):?>
        <?php
        /** @var  Classiqmodel $record */
        $record=$bean->box();
        ?>
        <?=$view->render("./browse-records-line",$record)?>
    <?endforeach;?>
</div>

