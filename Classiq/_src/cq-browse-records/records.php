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
        $className="Classiq\Models\\".$type;
        if(class_exists($className)) {
            $orderBy = $className::$DEFAULT_ORDER_BY;
            $type = strtolower($type);
            if ($keywords) {
                $collections[$type] = db()->find($type, "name LIKE '%$keywords%' ORDER BY $orderBy ");
                $total += db()->count($type, "name LIKE '%$keywords%' ORDER BY $orderBy ");
            } else {
                $collections[$type] = db()->find($type, "ORDER BY $orderBy ");
                $total += db()->count($type, "ORDER BY $orderBy ");
            }
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
<?php if(!$start):?>
<div class="cq-box">
<?php endif; ?>
    <?php foreach ($list as $bean):?>
        <?php
        /** @var  Classiqmodel $record */
        $record=$bean->box();
        ?>
        <?php echo $view->render("./browse-records-line",$record)?>
    <?php endforeach; ?>

    <?php if($next<$total):?>
    <div class="cq-box text-center" cq-replace-on-scrollview="<?php echo $nextUrl?>">
        <?php echo number_format($next)?> / <?php echo number_format($total)?>
        <div class="cq-loading-dots">
            <i class="a"></i>
            <i class="b"></i>
            <i class="c"></i>
        </div>
    </div>
    <?php else: ?>

    <?php endif; ?>

<?php if(!$start):?>
</div>
<?php endif; ?>

