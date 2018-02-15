<?php
the()->htmlLayout()->meta->title="Wysiwyg Doc";
$view->inside("layout");
$colors=["rien-du-tout","white","grey-light","grey-dark","black","danger"];

?>


<script>
    function go(){
        console.log("gen menu");
        let $items=$("#doc-sample").find("a[name]");
        let $menu=$("#menu-doc ul");
        $menu.empty();
        $items.each(function(){
            let $li=$("<li></li>");
            let $a=$(this).clone();
            $a.attr("href","#"+$(this).attr("name")).removeAttr("name");
            $li.append($a);

            $menu.append($li);
        });
    }

    setTimeout(
        function() {
            Pov.onBodyReady(go)
        },
    1000);
</script>
<style>
    #doc-sample{
        background-color: #fdffe0;
        color:#222;
    }
    #menu-doc{
        background-color: #fff;
        position: fixed;
        width: 200px;
        right: 70px;
        top:140px;
        padding: 15px;
        border: 1px solid #eee;
    }
    #menu-doc li{
        font-size: 10px;
        font-family: sans-serif;
        line-height: 1.5;
        margin-bottom: 5px;
    }
    #doc-sample{
        font-family: monospace;
        font-size: 16px;
        line-height: 1.5;
    }
    #doc-sample section{
        padding: 40px;
    }
    #doc-sample h1{
        font-size: 40px;
    }
    #doc-sample h2{
        font-size: 30px;
    }
    #doc-sample h3{
        font-size: 20px;
    }
    #doc-sample code{
        background-color: #333;
        color: #a4c567;
    }
    #doc-sample hr{
        border-style: dashed;
        border-color:#000;
    }


    #elements-dans-id-wysiwyg{
        background-color: #fff;
    }


</style>

<section id="doc-sample">

    <h1>Wysiwyg doc</h1>

    <nav id="menu-doc">
        <ul >
            <li><a href="#la-base">la base</a></li>
            <li><a href="#themes">themes</a></li>
            <li><a href="#tollbar">toolbar</a></li>
        </ul>
    </nav>


    <section id="elements-dans-le-dom">
        <h1><a name="hors-id-wysiwyg">Eléments hors <code>id='#the-cq-layer'</code></a>  </h1>
        <?=$view->render("quick-view/wysiwyg/doc/hors-id-wysiwyg")?>
    </section>

    <hr>

    <section id="elements-dans-id-the-cq-layer">

        <h1><a name="dans-id-wysiwyg">Eléments dans un <code>id='#the-cq-layer'</code></a>  </h1>

        <div id="the-cq-layer">

            <cq-design>
                <div class="wysiwyg-pad-xy">

                    <h1><a name="toolbar">Icones</a></h1>
                    <?=$view->render("quick-view/wysiwyg/doc/icones")?>

                    <hr>

                    <h1><a name="la-base">Eléments sans classe (h1,h2,p,hr, etc...)</a></h1>
                    <?=$view->render("quick-view/wysiwyg/doc/la-base")?>

                    <hr>

                    <h1><a name="la-base">Preview records</a></h1>
                    <?=$view->render("quick-view/wysiwyg/doc/preview-record")?>

                    <hr>

                    <h1><a name="la-base">Formulaires</a></h1>
                    <?=$view->render("quick-view/wysiwyg/doc/forms")?>

                    <hr>

                    <h1><a name="popin-box">Popin Box</a></h1>
                    <?=$view->render("quick-view/wysiwyg/doc/popin-box")?>

                    <hr>

                    <h1><a name="boutons">Boutons</a></h1>
                    <?=$view->render("quick-view/wysiwyg/doc/boutons")?>

                    <hr>

                    <h1><a name="alerts">Alerts</a></h1>
                    <?=$view->render("quick-view/wysiwyg/doc/alerts")?>

                    <hr>

                    <h1><a name="themes">Thèmes</a></h1>
                    <?=$view->render("quick-view/wysiwyg/doc/themes")?>

                    <hr>

                    <h1><a name="shadows">Shadows</a></h1>
                    <?=$view->render("quick-view/wysiwyg/doc/shadows")?>

                    <hr>

                    <h1><a name="toolbar">Toolbar</a></h1>
                    <?=$view->render("quick-view/wysiwyg/doc/toolbar")?>

                    <hr>

                </div>
            </cq-design>

        </div>

    </section>





</section>


