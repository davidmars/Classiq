<?php /**
 * Vue très minimale d'un record pour le wysiwyg
 * une icone à gauche
 * titre + types et id à droite
 */
?>
<div class="wysiwyg-preview-record">
    <span class="preview-icon"><?php echo cq()->icoWysiwyg("home")?></span>
    <div>
        <div class="preview-title">Titre de mon record qui va bien</div>
        <div class="preview-type">Type de record@id</div>
    </div>
</div>

<div class="cq-cols">
    <?php for ($c=0;$c<5;$c++):?>
        <div class="cq-col-3">
            <?php for ($i=0;$i<10;$i++):?>
                <div class="wysiwyg-preview-record">
                    <span class="preview-icon"><?php echo cq()->icoWysiwyg("home")?></span>
                    <div>
                        <div class="preview-title"><?php echo pov()->utils->string->loremIspum(2,15)?></div>
                        <div class="preview-type">Type de record@id</div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    <?php endfor; ?>
</div>

