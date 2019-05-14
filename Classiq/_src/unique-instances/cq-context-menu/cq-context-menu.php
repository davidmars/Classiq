<div id="the-cq-context-menu" style="display:none">

    <div>

        <div id="menu-and-selection">

            <?//rectangle de selection à la photoshop ?>
            <div id="the-cq-selection"></div>

            <?//menu volant d'ajout ?>
            <div cq-btn-group class="is-menu-add cq-th-white">
                <a href="#plus"><?=pov()->svg->use("cq-plus")?></a>
                <a class="input-file-wrap" href="#plus-upload">
                    <div class="cq-motion alternate">
                        <?=pov()->svg->use("cq-plus")?>
                        <?=pov()->svg->use("cq-images-photo")?>
                    </div>

                    <input type="file" multiple="multiple">
                </a>
                <a class="input-file-wrap" href="#upload">
                    <?=pov()->svg->use("cq-upload")?>
                    <input type="file">
                </a>
            </div>



            <?//menu volant ?>
            <div cq-btn-group class="is-menu cq-th-white">

                <span style="display:none;"
                      title="nom du block"
                      class="ico js-preview-icon cq-fg-disabled">
                    <?=pov()->svg->use("cq-sad")?>
                </span>

                <a href="#up"><?=pov()->svg->use("cq-arrow-up")?></a>
                <a href="#down"><?=pov()->svg->use("cq-arrow-down")?></a>
                <a href="#left"><?=pov()->svg->use("cq-arrow-left")?></a>
                <a href="#right"><?=pov()->svg->use("cq-arrow-right")?></a>
                <a href="#cog"><?=pov()->svg->use("cq-cog")?></a>
                <a href="#trash"><?=pov()->svg->use("cq-trash")?></a>
            </div>

        </div>

        <?
        /*
         * Layer qui permet de désactiver les clicks roll over etc... sur le reste du DOM
         */
        ?>
        <a href="#hide-stuff" class="disabler"></a>
        <?
        /*
         * La fenêtre où on charge les configurations
         */
        ?>
        <div id="config-box" cq-popin-box class="medium">
            <nav>
                <h4><?=cq()->tradWysiwyg("Configurer")?></h4>
                <a href="#hide-stuff"><?=pov()->svg->use("cq-close")?></a>
            </nav>
            <main></main>
        </div>
        <?
        /*
         * La fenêtre qui contient le selectionneur de records
         */
        ?>
        <?=$this->render("../cq-record-picker/cq-record-picker")?>


    </div>

</div>