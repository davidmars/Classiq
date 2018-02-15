<div id="the-cq-record-picker" cq-popin-box class="medium" >

    <nav>
        <h4>Choisissez</h4>
        <a href="#hide-closest-box"><?=cq()->icoWysiwyg("close")?></a>
    </nav>

    <main>
        <?=$view->render("./cq-record-picker-list")?>
    </main>

    <footer>
        <span></span>
        <span>
            <button href="#hide-closest-box" class="js-btn-cancel cq-btn cq-th-white small">
                <?=cq()->icoWysiwyg("close")?>
                <span>annuler</span>
            </button>
            <button class="js-btn-ok cq-btn cq-th-black small">
                <?=cq()->icoWysiwyg("check")?>
                <span>ok</span>
            </button>
        </span>
    </footer>

</div>