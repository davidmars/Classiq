<script>
    var CqLayer = document.registerElement('cq-layer');
    //document.body.appendChild(new CqLayer());
</script>
<cq-layer id="the-cq-layer" class="cq-vars-th-white cq-css" history-hrefs>
    <?php echo $view->render("./cq-big-menu/cq-big-menu")?>
    <?php echo $view->render("./cq-context-menu/cq-context-menu")?>
    <?php echo $view->render("./cq-notifier/wysiwyg-notifier")?>
    <div style="display: none">
        <?php echo pov()->svg->import("dist/svg-collection/cq.svg")?>
    </div>
    <?php echo $view->render("./cq-edit-record-box/cq-edit-record-box")?>
</cq-layer>

<?php //------css traductions------------------?>
<style>
    <?php foreach (the()->project->languages as $lang):?>
    .fld[data-lang='<?php echo $lang?>'],label[data-lang='<?php echo $lang?>']{
        background-image: url('<?php echo pov()->utils->lang->flagUrl($lang)?>') !important;
    }
    <?php endforeach; ?>
</style>
