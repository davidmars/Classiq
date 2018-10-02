<?php

use Classiq\Wysiwyg\WysiwygConfig;



//TODO important state draft / visible / invisible

?>
<?if(the()->human->isAdmin):?>
    <div id="the-cq-big-menu" class="cq-th-white" cq-panel-section="">
        <div class="wrap">
            <nav cq-toolbar class=" cq-th-white">
                <div>

                    <a cq-panel-section-toggle="page" href="#"
                       class=""
                       title="Éditer la page en cours">
                        <?=pov()->svg->use("cq-edit")?>
                        <i cq-tip data-count="0" class="cq-th-danger"></i>
                    </a>
                    <a cq-panel-section-toggle="create" href="#"
                       class=""
                       title="Créer une nouvelle page">
                        <?=pov()->svg->use("cq-circle-plus")?>
                    </a>
                    <a cq-panel-section-toggle="browse" href="#"
                       title="Rechercher & éditer des élements du site">
                        <?=pov()->svg->use("cq-search")?>
                    </a>
                    <a cq-panel-section-toggle="config" href="#"
                       class=""
                       title="Paramètres">
                        <?=pov()->svg->use("cq-cog")?>
                    </a>
                    <a cq-panel-section-toggle="user" href="#"
                       class=""
                       title="Déconnexion et gestion des utilisateurs">
                        <?=pov()->svg->use("cq-user-group")?>
                        <i cq-tip data-count="0" class="cq-th-danger"></i>
                    </a>


                </div>
                <a href="#" cq-on-click="ui.bigMenu.toggle()"><?=pov()->svg->use("cq-close")?></a>
            </nav>
            <main>
                <!----contenu scrollable-- -->
                <div cq-panel-is-section="page" class="section-active"></div>

                <section cq-panel-is-section="create">
                    <label>Créer une nouvelle page :</label>
                    <?=$view->render("cq-new-record/cq-new-record",
                        [
                                "types"=>WysiwygConfig::inst()->recordsWeCanCreate,
                                "placeholder"=>"Nom de la nouvelle page"
                        ]

                    )?>
                </section>

                <?=$view->render("./user-section")?>

                <?=$view->render("cq-browse-records/browse-records")?>

                <section cq-panel-is-section="config">
                    <?=$view->render("./config-box")?>
                </section>

            </main>
        </div>
        <div class="out-btns">

            <a cq-panel-section-toggle="page" href="#" cq-on-click="ui.bigMenu.toggle()"
               class="cq-btn circle cq-th-white"
               title="Éditer la page en cours">
                <?=pov()->svg->use("cq-edit")?>
                <i cq-tip data-count="0" class="cq-th-danger"></i>
            </a>
            <a cq-panel-section-toggle="create" href="#" cq-on-click="ui.bigMenu.toggle()"
               class="cq-btn circle cq-th-white"
               title="Créer une nouvelle page">
                <?=pov()->svg->use("cq-plus")?>
            </a>
            <a cq-panel-section-toggle="browse" href="#" cq-on-click="ui.bigMenu.toggle()"
               class="cq-btn circle cq-th-white"
               title="Rechercher & éditer des élements du site">
                <?=pov()->svg->use("cq-search")?>
            </a>
            <a cq-panel-section-toggle="config" href="#" cq-on-click="ui.bigMenu.toggle()"
               class="cq-btn circle cq-th-white"
               title="Paramètres">
                <?=pov()->svg->use("cq-cog")?>
            </a>
            <a cq-panel-section-toggle="user" href="#" cq-on-click="ui.bigMenu.toggle()"
               class="cq-btn circle cq-th-white"
               title="Déconnexion et gestion des utilisateurs">
                <?=pov()->svg->use("cq-user-group")?>
                <i cq-tip data-count="0" class="cq-th-danger"></i>
            </a>

        </div>
        <div class="extension">

        </div>



    </div>
<?endif?>