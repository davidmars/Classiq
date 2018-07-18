<?
/**
 * Efface tous les urlpage qui n'ont pas de pages relative
 */

/** @var Urlpage[] $urls */

use Classiq\Models\Urlpage;
$doit= the()->request("je-suis-certain") === "1";
?>
<?if(the()->human->isDev(true)):?>


    <?=$view->render("./menu")?>

    <?php
        $urls=db()->find("urlpage");
        $totalTodelete=0;
        $totalOk=0;
        $total=count($urls);
    ?>

    <h4><?=$total?> Urls</h4>
    <?foreach ($urls as $f):?>

        <?=$f->url_lang?><br>
        <code>(<?=$f->uid()?>)</code><br>
        Type de page : <code>(<?=$f->related_type?>)</code><br>
        <code><?=$f->localPath()?></code><br>
        <?if(!$f->getPage(false)):?>
            <?php $totalTodelete++ ?>
            <b style='color:red;'>Page introuvable</b><br>
            <?if($doit):?>
                <? db()->trash($f) ?>
                <b style='color:red;'>Effacé</b><br>
            <?else:?>
                <b style='color:blue;'>url pas effacée</b><br>
            <?endif?>
        <?else:?>
            <?php $totalOk++ ?>
        <?endif;?>

        <hr>



    <?endforeach;?>




    <?if(!$doit):?>
        <hr>
        <?=$totalOk?> urls sans soucis.<br>
        <?=$totalTodelete?> urls à effacer.<br>
        <hr>
        <form method="post">
            <input type="hidden" name="je-suis-certain" value="1">
            <input type="submit" value="Effacer les <?=$totalTodelete?> url">
        </form>
    <?endif?>

<?else:?>
Il faut être loggué en dev.
<?endif?>









