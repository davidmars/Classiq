<script>
    var CqLayer = document.registerElement('cq-layer');
    //document.body.appendChild(new CqLayer());
</script>
<cq-layer id="the-cq-layer" class="cq-vars-th-white cq-css" history-hrefs>
    <?=$view->render("./cq-big-menu/cq-big-menu")?>
    <?=$view->render("./cq-context-menu/cq-context-menu")?>
    <?=$view->render("./cq-notifier/wysiwyg-notifier")?>
    <div style="display: none">
        <?=pov()->svg->import("dist/svg-collection/cq.svg")?>
    </div>
    <?=$view->render("./cq-edit-record-box/cq-edit-record-box")?>
</cq-layer>

<?//------css traductions------------------?>
<style>
    <?foreach (the()->project->languages as $lang):?>
    .fld[data-lang='<?=$lang?>'],label[data-lang='<?=$lang?>']{
        background-image: url('<?=pov()->utils->lang->flagUrl($lang)?>') !important;
    }
    <?endforeach;?>
</style>
