<?
/**
 * Fait le tour des urls pour nettoyer les pas jolies
 */

/** @var Urlpage[] $urls */

use Classiq\Models\Urlpage;
$doit= the()->request("je-suis-certain") === "1";

/**
 * @param string $url
 * @param stran $lang
 * @param Urlpage $bean
 * @return string
 */
function uggly($url,$lang,$bean){
    $moche=false;
    if($bean && $bean->is_homepage){
        return "";
    }

    if(!preg_match("/[a-z]+/",$url,$m)){
        $moche=true;
    }
    if($moche && $bean && $bean->getPage(false)){
        $var="name_$lang";
        $repl=$bean->getPage(false)->$var;
        if(!$repl){
            $repl=$bean->getPage(false)->name;
        }
        if($repl){
            $repl=strtolower(utils()->string->clean($repl));
            $repl=trim($repl,"-/ _");
            return $repl;
        }

    }


    return "";
}

?>
<?if(the()->human->isDev(true)):?>


    <?=$view->render("./menu")?>

    <?php
        $urls=db()->find("urlpage");
        $totalChange=0;
        $totalOk=0;
        $total=count($urls);
    ?>

    <h4><?=$total?> Urls</h4>
    <table>

        <tr>
            <th>Name de la page</th>
            <th>uid</th>
            <th>type</th>
            <th>priorité</th>
            <?foreach (the()->project->languages as $lang):?>
                <th>url <?=$lang?></th>
                <th class="replacement">remplacement <?=$lang?></th>
            <?endforeach;?>
        </tr>


        <?foreach ($urls as $f):?>
            <tr>
                <td>
                    <?if(!$f->getPage(false)):?>
                        <b style='color:red;'>Page introuvable</b><br>
                    <?else:?>
                        <?=$f->getPage(false)->name?>
                    <?endif;?>
                </td>
                <td><?=$f->uid()?></td>
                <td><?=$f->related_type?></td>
                <td><?=$f->seo_priority?></td>
            <?foreach (the()->project->languages as $lang):?>
                <td>
                    <?php
                    $var="url_$lang";
                    ?>
                    <?=$f->$var?>
                </td>
                <td class="replacement">
                    <?php
                    $remplacement=uggly($f->$var,$lang,$f);
                    ?>
                    <?=$remplacement?>
                    <?if($remplacement):?>
                        <?php
                            $totalChange++;
                        ?>
                        <?if($doit):?>
                            <?php
                                $f->$var=$remplacement;
                                db()->store($f);
                            ?>
                            <br>L'url A été remplacée !
                        <?else:?>
                            <br>L'url n'a pas été remplacée
                        <?endif?>
                    <?else:?>
                        <?php
                        $totalOk++;
                        ?>
                    <?endif?>
                </td>
            <?endforeach?>


            </tr>

        <?endforeach;?>
    </table>

    <style>
        table{
            border: 1px solid #666;
            border-collapse: collapse;
        }
        td,th{
            font-family: monospace;
            font-size: 12px;
            border: 1px solid #666;
            padding: 5px;
        }
        .replacement{
            color: #f00;
        }
    </style>



    <?if(!$doit):?>
        <hr>
        <?=$totalOk?> urls sans soucis.<br>
        <?=$totalChange?> urls à modifier.<br>
        <hr>
        <form method="post">
            <input type="hidden" name="je-suis-certain" value="1">
            <input type="submit" value="Modifier les <?=$totalChange?> url">
        </form>
    <?endif?>

<?else:?>
Il faut être loggué en dev.
<?endif?>









