<div id="the-cq-context-menu" style="display:none">

    <div>

        <div id="menu-and-selection">

            <?//rectangle de selection à la photoshop ?>
            <div id="the-cq-selection"></div>

            <?//menu volant d'ajout ?>
            <div cq-btn-group class="is-menu-add cq-th-white">
                <a href="#plus"><?=cq()->icoWysiwyg("plus")?></a>
                <a class="input-file-wrap" href="#plus-upload">
                    <div class="cq-motion alternate">
                        <?=cq()->icoWysiwyg("plus")?>
                        <?=cq()->icoWysiwyg("images-photo")?>
                    </div>

                    <input type="file" multiple="multiple">
                </a>
                <a class="input-file-wrap" href="#upload">
                    <?=cq()->icoWysiwyg("upload")?>
                    <input type="file">
                </a>
            </div>

            <?//menu volant ?>
            <div cq-btn-group class="is-menu cq-th-white">
                <a href="#up"><?=cq()->icoWysiwyg("arrow-up")?></a>
                <a href="#down"><?=cq()->icoWysiwyg("arrow-down")?></a>
                <a href="#left"><?=cq()->icoWysiwyg("arrow-left")?></a>
                <a href="#right"><?=cq()->icoWysiwyg("arrow-right")?></a>
                <a href="#cog"><?=cq()->icoWysiwyg("cog")?></a>
                <a href="#trash"><?=cq()->icoWysiwyg("trash")?></a>
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
                <h4>Configurer</h4>
                <a href="#hide-stuff"><?=cq()->icoWysiwyg("close")?></a>
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