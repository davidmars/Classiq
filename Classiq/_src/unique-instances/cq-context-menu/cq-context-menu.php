<div id="the-cq-context-menu" style="display:none">

    <div>

        <div id="menu-and-selection">

            <?php //rectangle de selection à la photoshop ?>
            <div id="the-cq-selection"></div>

            <?php //menu volant d'ajout ?>
            <div cq-btn-group class="is-menu-add cq-th-white">
                <a href="#plus"><?php echo pov()->svg->use("cq-plus")?></a>
                <a class="input-file-wrap" href="#plus-upload">
                    <div class="cq-motion alternate">
                        <?php echo pov()->svg->use("cq-plus")?>
                        <?php echo pov()->svg->use("cq-images-photo")?>
                    </div>

                    <input type="file" multiple="multiple">
                </a>
                <a class="input-file-wrap" href="#upload">
                    <?php echo pov()->svg->use("cq-upload")?>
                    <input type="file">
                </a>
            </div>



            <?php //menu volant des blocks?>
            <div cq-btn-group class="is-menu cq-th-white">

                <span style="display:none;"
                      title="nom du block"
                      class="ico js-preview-icon cq-fg-disabled">
                    <?php echo pov()->svg->use("cq-sad")?>
                </span>

                <span data-pov-refresh-method="html" class="js-custom-menu" cq-btn-sub-group>
                    <?php //les boutons de config menus customs viennent ici?>
                </span>
                <a href="#up"><?php echo pov()->svg->use("cq-arrow-up")?></a>
                <a href="#down"><?php echo pov()->svg->use("cq-arrow-down")?></a>
                <a href="#left"><?php echo pov()->svg->use("cq-arrow-left")?></a>
                <a href="#right"><?php echo pov()->svg->use("cq-arrow-right")?></a>
                <a href="#cog"><?php echo pov()->svg->use("cq-cog")?></a>
                <a href="#trash"><?php echo pov()->svg->use("cq-trash")?></a>
            </div>

        </div>

        <?php 
        /*
         * Layer qui permet de désactiver les clicks roll over etc... sur le reste du DOM
         */
        ?>
        <a href="#hide-stuff" class="disabler"></a>
        <?php 
        /*
         * La fenêtre où on charge les configurations
         */
        ?>
        <div id="config-box" cq-popin-box class="medium">
            <nav>
                <h4><?php echo cq()->tradWysiwyg("Configurer")?></h4>
                <a href="#hide-stuff"><?php echo pov()->svg->use("cq-close")?></a>
            </nav>
            <main></main>
        </div>
        <?php 
        /*
         * La fenêtre qui contient le selectionneur de records
         */
        ?>
        <?php echo $this->render("../cq-record-picker/cq-record-picker")?>


    </div>

</div>