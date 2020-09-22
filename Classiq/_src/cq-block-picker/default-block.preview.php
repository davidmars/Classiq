<?php
if(is_string($vv)){
    $label=$vv;
}else{
    $label="...";
}
?>
<label><?php echo $label?></label>
<?php echo pov()->svg->use("cq-plus")?>