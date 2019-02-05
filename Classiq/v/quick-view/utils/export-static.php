<?php
use Classiq\Models\Page;
$pathToReplace=the()->configProjectUrl->httpPathNoHost;
$pathToReplace=preg_replace("/\/".the()->project->langCode."$/","",$pathToReplace);
$pathToReplace=trim($pathToReplace,"/");
$pathToReplace="/".$pathToReplace."/";
?>
<form>
    <h1>Ce module permet d'exporter les pages du site vers des fichiers statiques</h1>
    <p>
        Les urls <code><?=$pathToReplace?></code> seront remplacées par <code>"./"</code> dans les pages.<br>
        La home page s'apellera index.html.<br>
    </p>
    <?if(\Classiq\Wysiwyg\Wysiwyg::$enabled || the()->human->isAdmin):?>
        <h3>Veuillez vous déconnecter</h3>
    <?else:?>
        <p>Voulez-vous continuer?</p>
        <button type="submit" name="doIt" value="1">Oui</button>
    <?endif?>

</form>

<?if(the()->request("doIt")):?>
<?php
/** @var Page[] $pages */



$pages=db()->findAll("page");
foreach ($pages as $p){
    $url="_export-static".$p->href()->relative();
    if($p->urlpage->is_homepage){
        $url.="index";
    }
    echo $url."<br>";



    the()->fileSystem->prepareDir($url);
    $content=file_get_contents($p->href()->absolute()."?exportStatic=1");
    $content=preg_replace("/".preg_quote($pathToReplace,'/')."/","../",$content);
    file_put_contents($url.".html",$content);

    $content=file_get_contents($p->href()->absolute()."?exportStatic=1&povHistory=1");
    $content=preg_replace("/\/github\/changemakers\//","../",$content);
    file_put_contents($url.".html.json",$content);
    echo "<textarea>".$content."</textarea><br>";





}
?>
<?endif?>
<hr>
<?=$view->render("./menu")?>
