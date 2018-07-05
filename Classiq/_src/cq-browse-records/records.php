<?php
use Classiq\Models\Classiqmodel;

$types=the()->request("types");
$keywords=the()->request("keywords");
$collections = [];

$total=0;
$start=the()->request("start",0);
$slice=100;
$next=$start+$slice;

if($types){
    $types=explode(",",$types);
    foreach ($types as $type){
        $type=strtolower($type);
        if($keywords){
            $collections[$type]=db()->find($type,"name LIKE '%$keywords%' ORDER BY name");
            $total+=db()->count($type,"name LIKE '%$keywords%' ORDER BY name");
        }else{
            $collections[$type]=db()->find($type,"ORDER BY name");
            $total+=db()->count($type,"ORDER BY name");
        }

    }
}

$list=[];
foreach ($collections as $type=>$beans){
    foreach ($beans as $bean){
        $list[]=$bean;
    }
}
$list=array_slice($list,$start,$slice);
$i=$start;

$nextUrl="".the()->request("viewPath")."?";
$nextUrl.="&start=$next";
$nextUrl.="&types=".the()->request("types");
$nextUrl.="&keywords=".the()->request("keywords");


?>
<?if(!$start):?>
<div class="cq-box">
<?endif;?>
    <?foreach ($list as $bean):?>
        <?php
        /** @var  Classiqmodel $record */
        $record=$bean->box();
        ?>
        <?=$view->render("./browse-records-line",$record)?>
    <?endforeach;?>

    <?if($next<$total):?>
    <div class="cq-box text-center" cq-replace-on-scrollview="<?=$nextUrl?>">
        <?=number_format($next)?> / <?=number_format($total)?>
        <div class="cq-loading-dots">
            <i class="a"></i>
            <i class="b"></i>
            <i class="c"></i>
        </div>
    </div>
    <?else:?>

    <?endif?>

<?if(!$start):?>
</div>
<?endif;?>

