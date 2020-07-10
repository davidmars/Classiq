<?php

use Classiq\Wysiwyg\WysiwygConfig;



//TODO important state draft / visible / invisible

?>
<?php if(the()->human->isAdmin):?>
    <div id="the-cq-big-menu" class="cq-th-white" cq-panel-section="">
        <div class="wrap">
            <nav cq-toolbar class=" cq-th-white">
                <div>

                    <a cq-panel-section-toggle="page" href="#"
                       class=""
                       title="<?php echo cq()->tradWysiwyg("Éditer la page en cours")?>">
                        <?php echo pov()->svg->use("cq-edit")?>
                        <i cq-tip data-count="0" class="cq-th-danger"></i>
                    </a>
                    <a cq-panel-section-toggle="create" href="#"
                       class=""
                       title="<?php echo cq()->tradWysiwyg("Créer une nouvelle page")?>">
                        <?php echo pov()->svg->use("cq-circle-plus")?>
                    </a>
                    <a cq-panel-section-toggle="browse" href="#"
                       title="<?php echo cq()->tradWysiwyg("Rechercher & éditer des élements du site")?>">
                        <?php echo pov()->svg->use("cq-search")?>
                    </a>
                    <a cq-panel-section-toggle="config" href="#"
                       class=""
                       title="<?php echo cq()->tradWysiwyg("Paramètres")?>">
                        <?php echo pov()->svg->use("cq-cog")?>
                    </a>
                    <a cq-panel-section-toggle="user" href="#"
                       class=""
                       title="<?php echo cq()->tradWysiwyg("Déconnexion et gestion des utilisateurs")?>">
                        <?php echo pov()->svg->use("cq-user-group")?>
                        <i cq-tip data-count="0" class="cq-th-danger"></i>
                    </a>


                </div>
                <a href="#" cq-on-click="ui.bigMenu.toggle()"><?php echo pov()->svg->use("cq-close")?></a>
            </nav>
            <main>
                <!----contenu scrollable-- -->
                <div cq-panel-is-section="page" class="section-active"></div>

                <section cq-panel-is-section="create">
                    <label><?php echo cq()->tradWysiwyg("Créer une nouvelle page")?> :</label>
                    <?php echo $view->render("cq-new-record/cq-new-record",
                        [
                                "types"=>WysiwygConfig::inst()->recordsWeCanCreate,
                                "placeholder"=>cq()->tradWysiwyg("Nom de la nouvelle page")
                        ]

                    )?>
                </section>

                <?php echo $view->render("./user-section")?>

                <?php echo $view->render("cq-browse-records/browse-records")?>

                <section cq-panel-is-section="config">
                    <?php echo $view->render("./config-box")?>
                </section>

            </main>
        </div>
        <div class="out-btns">

            <a cq-panel-section-toggle="page" href="#" cq-on-click="ui.bigMenu.toggle()"
               class="cq-btn circle cq-th-white"
               title="<?php echo cq()->tradWysiwyg("Éditer la page en cours")?>">
                <?php echo pov()->svg->use("cq-edit")?>
                <i cq-tip data-count="0" class="cq-th-danger"></i>
            </a>
            <a cq-panel-section-toggle="create" href="#" cq-on-click="ui.bigMenu.toggle()"
               class="cq-btn circle cq-th-white"
               title="<?php echo cq()->tradWysiwyg("Créer une nouvelle page")?>">
                <?php echo pov()->svg->use("cq-plus")?>
            </a>
            <a cq-panel-section-toggle="browse" href="#" cq-on-click="ui.bigMenu.toggle()"
               class="cq-btn circle cq-th-white"
               title="<?php echo cq()->tradWysiwyg("Rechercher & éditer des élements du site")?>">
                <?php echo pov()->svg->use("cq-search")?>
            </a>
            <a cq-panel-section-toggle="config" href="#" cq-on-click="ui.bigMenu.toggle()"
               class="cq-btn circle cq-th-white"
               title="<?php echo cq()->tradWysiwyg("Paramètres")?>">
                <?php echo pov()->svg->use("cq-cog")?>
            </a>
            <a cq-panel-section-toggle="user" href="#" cq-on-click="ui.bigMenu.toggle()"
               class="cq-btn circle cq-th-white"
               title="<?php echo cq()->tradWysiwyg("Déconnexion et gestion des utilisateurs")?>">
                <?php echo pov()->svg->use("cq-user-group")?>
                <i cq-tip data-count="0" class="cq-th-danger"></i>
            </a>

        </div>
        <div class="extension">

        </div>



    </div>
<?php endif; ?>