<?php
/**
 * @var array $vv [value,imgUrl,domRatioSelector,ratio]
 */

/** @var string $value Le rectangle de crop à appliquer au départ au format json encodé */
$value=$vv["value"];
/** @var string $imgUrl Url de l'image à charger*/
$imgUrl=$vv["imgUrl"];
/** @var float $ratio Pour forcer le ratio */
$ratio=$vv["ratio"];
/** @var string $domRatioSelector Un selecteur jQuery qui permettra de déduire le ratio d'image à appliquer */
$domRatioSelector=$vv["domRatioSelector"];


?>

<div cq-field-crop
     data-ratio-target-selector="<?=$domRatioSelector?>"
     data-ratio="<?=$ratio?>"
    >

    <img  style="max-height: 400px; display: block" src="<?=$imgUrl?>">
    <textarea class="fld" rows="1"><?=$value?></textarea>
</div>