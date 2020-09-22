<?php

use Classiq\Models\User;

$view->inside("layout/html5bp",the()->htmlLayout());
the()->htmlLayout()->addCssToHeader("vendor/davidmars/pov-2018/dist/dev-ui.css");
the()->htmlLayout()->addJsToFooter("vendor/davidmars/pov-2018/dist/dev-ui.js");

?>
<div class="uk-text-small uk-text-right uk-text-muted uk-padding-small">
    <?php if(User::connected()):?>
        Connecté en tant que <b><?php echo User::connected()->email?></b>
    <?php else: ?>
        Non connecté
    <?php endif; ?>
</div>
<div style="min-height: 90vh" class="uk-flex uk-flex-middle ">
    <div class="uk-flex-auto uk-container uk-container-small uk-margin-top uk-text-center uk-flex uk-flex-center">
        <div class="uk-card uk-card-default uk-card-body uk-width-1-2@m">
            <?php echo $view->insideContent?>
        </div>
    </div>
</div>

