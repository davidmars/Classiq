<style>
    [cq-popin-box]{
        /** pour la doc **/
        position: relative !important;
        margin-bottom: 30px;
    }
</style>
<div style="position: relative" class="wysiwyg-cols">

    <div>
        <div cq-popin-box class="open">
            <nav>
                <h4>Titre de la box</h4> <a href="">
                    <?php echo cq()->icoWysiwyg("close")?>
                </a>
            </nav>
            <content>

            </content>
            <footer>
                <i></i>
                <a class="cq-btn cq-th-white" href="#">Test</a>
            </footer>
        </div>
    </div>

    <div>
        <div cq-popin-box class=" open">
            <nav>
                <h4>Titre de la box</h4> <a href="">
                    <?php echo cq()->icoWysiwyg("close")?>
                </a>
            </nav>
            <content>
                <h2>Le contenu scrolle au besoin</h2>
                <?php echo pov()->utils->string->loremIspum(2000)?>
            </content>
            <footer>
                <i></i>
                <a class="cq-btn cq-th-white" href="#">Test</a>
            </footer>
        </div>
    </div>

    <div>
        <div cq-popin-box class=" medium open">
            <nav>
                <h4>Box medium</h4> <a href=""><?php echo cq()->icoWysiwyg("close")?></a>
            </nav>
            <content>
                ...
            </content>
            <footer>
                <div>Texte aditionnel</div>
                <div>
                <a class="cq-btn cq-th-white" href="#">cancel</a>
                <a class="cq-btn cq-th-white" href="#">ok</a>
                </div>
            </footer>
        </div>
    </div>




</div>
