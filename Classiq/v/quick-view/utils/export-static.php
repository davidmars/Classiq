<?php
use Classiq\Models\Page;
$pathToReplace=the()->configProjectUrl->httpPathNoHost;
$pathToReplace=preg_replace("/\/".the()->project->langCode."$/","",$pathToReplace);
$pathToReplace=trim($pathToReplace,"/");
$pathToReplace="/".$pathToReplace."/";
set_time_limit(60*10);

?>
<style>
    code{
        background-color: #f0f0f0;
        color: #227cab;
        padding: 0px 5px;
        border-radius: 4px;
    }
</style>


<?php
$replaceAbsolute=the()->request("absoluteUrl","");
$moreQueryString=the()->request("moreQueryString","");
$exportDir=the()->request("exportDir","");
?>

<form>
    <h1>Ce module permet d'exporter les pages du site vers des fichiers statiques</h1>
    <p>
        Les urls <code><?php echo $pathToReplace?></code> seront remplacées par <code>"./"</code> dans les pages.<br>
        La home page s'apellera index.html.<br>
    </p>

    <fieldset>
        <p>Remplacer
            <code><?php echo the()->requestUrl->httpAndHost.the()->fmkHttpRoot?></code>
            par...
            <input name="absoluteUrl" style="display: block;width: 80%;" type="text" value="<?php echo $replaceAbsolute?>">
        </p>
        <p>Exporter vers le répertoire...
            <input name="exportDir" style="display: block;width: 80%;" type="text" value="<?php echo $exportDir?>">
        </p>
        <p>query string additifs...
            <input placeholder="&param=value" name="moreQueryString" style="display: block;width: 80%;" value="<?php echo $moreQueryString?>" type="text">
        </p>
    </fieldset>
    <p>Voulez-vous continuer?</p>
    <button type="submit" name="doIt" value="1">Oui</button>


</form>

<?php if(the()->request("doIt")):?>
<?php
/** @var Page[] $pages */



$pages=db()->findAll("page");

if($exportDir){
    $exportDir=trim($exportDir,"/");
    $exportDir="/$exportDir";
}

foreach ($pages as $p){
    $url="_export-static".$exportDir.$p->href()->relative();
    if($p->urlpage->is_homepage){
        $url.="index";
    }
    //echo $url."<br>";

    //exporte les pages html
    the()->fileSystem->prepareDir($url);

    //.html
    $params="?exportStatic=1&$moreQueryString";
    $full=$p->href()->absolute().$params;
    echo $full."<br>";
    $content=file_get_contents($full);
    if($replaceAbsolute){
        $content=preg_replace("/".preg_quote(the()->requestUrl->httpAndHost.the()->fmkHttpRoot,'/')."/","$replaceAbsolute",$content);
    }
    $content=preg_replace("/".preg_quote($pathToReplace,'/')."/","../",$content);
    $content=preg_replace("/".preg_quote($params,'/')."/","",$content);
    file_put_contents($url.".html",$content);

    //.json
    $params="?exportStatic=1&povHistory=1&$moreQueryString";
    $full=$p->href()->absolute().$params;
    $content=file_get_contents($full);
    if($replaceAbsolute){
        $content=preg_replace("/".preg_quote(the()->requestUrl->httpAndHost.the()->fmkHttpRoot,'/')."/","$replaceAbsolute",$content);
    }
    $content=preg_replace("/".preg_quote($pathToReplace,'/')."/","../",$content);
    $content=preg_replace("/".preg_quote($params,'/')."/","",$content);
    file_put_contents($url.".html.pov.json",$content);
    echo "<div>".the()->boot->getTime()."</div>";
    echo "<textarea>".$content."</textarea><br>";

}
?>
<?php endif; ?>
<hr>
<?php echo $view->render("./menu")?>
