<?php
use Classiq\Models\Page;
$pathToReplace=the()->configProjectUrl->httpPathNoHost;
$pathToReplace=preg_replace("/\/".the()->project->langCode."$/","",$pathToReplace);
$pathToReplace=trim($pathToReplace,"/");
$pathToReplace="/".$pathToReplace."/";
set_time_limit(60*5);

//

/*

http://localhost/github/changemakers/   *** https://blog-tom.com/webdoc/changemakers
/github/changemakers/fr/                *** ./
/github/changemakers/                   *** ../


/blog-tom.com/webdoc/changemakers
blog-tom.com/webdoc/changemakers/fr


*/

?>
<style>
    code{
        background-color: #f0f0f0;
        color: #227cab;
        padding: 0px 5px;
        border-radius: 4px;
    }
</style>




<form>
    <h1>Ce module permet d'exporter les pages du site vers des fichiers statiques</h1>
    <p>
        Les urls <code><?=$pathToReplace?></code> seront remplacées par <code>"./"</code> dans les pages.<br>
        La home page s'apellera index.html.<br>
    </p>

    <fieldset>
        <p>Remplacer
            <code><?=the()->requestUrl->httpAndHost.the()->fmkHttpRoot?></code>
            par...
            <input name="absoluteUrl" style="display: block;width: 80%;" type="text">
        </p>
    </fieldset>
    <p>Voulez-vous continuer?</p>
    <button type="submit" name="doIt" value="1">Oui</button>


</form>

<?if(the()->request("doIt")):?>
<?php
/** @var Page[] $pages */



$pages=db()->findAll("page");
$replaceAbsolute=the()->request("absoluteUrl");

foreach ($pages as $p){
    $url="_export-static".$p->href()->relative();
    if($p->urlpage->is_homepage){
        $url.="index";
    }
    //echo $url."<br>";

    //exporte les pages html
    the()->fileSystem->prepareDir($url);

    $params="?exportStatic=1";
    $full=$p->href()->absolute().$params;
    echo $full."<br>";

    //.html
    $content=file_get_contents($full);
    if($replaceAbsolute){
        $content=preg_replace("/".preg_quote(the()->requestUrl->httpAndHost.the()->fmkHttpRoot,'/')."/","$replaceAbsolute",$content);
    }
    $content=preg_replace("/".preg_quote($pathToReplace,'/')."/","../",$content);
    $content=preg_replace("/".preg_quote($params,'/')."/","",$content);
    file_put_contents($url.".html",$content);

    //.json
    $params="?exportStatic=1&povHistory=1";
    $full=$p->href()->absolute().$params;
    $content=file_get_contents($full);
    if($replaceAbsolute){
        $content=preg_replace("/".preg_quote(the()->configProjectUrl->absoluteUrl(),'/')."/","$replaceAbsolute",$content);
    }
    $content=preg_replace("/".preg_quote($pathToReplace,'/')."/","../",$content);
    $content=preg_replace("/".preg_quote($params,'/')."/","",$content);
    file_put_contents($url.".html.pov.json",$content);
    echo "<textarea>".$content."</textarea><br>";

}
?>
<?endif?>
<hr>
<?=$view->render("./menu")?>
