<?php

use Classiq\Models\User;

$view->inside("layout/html5bp",the()->htmlLayout());
the()->htmlLayout()->addCssToHeader(\Classiq\Classiq::assetsDir()."/login.css");
the()->htmlLayout()->addJsToFooter(\Classiq\Classiq::assetsDir()."/login.js");

?>
<div class="uk-text-small uk-text-right uk-text-muted uk-padding-small">
    <?if(User::connected()):?>
        Connecté en tant que <b><?=User::connected()->email?></b>
    <?else:?>
        Non connecté
    <?endif;?>
</div>
<div style="min-height: 90vh" class="uk-flex uk-flex-middle ">
    <div class="uk-flex-auto uk-container uk-container-small uk-margin-top uk-text-center uk-flex uk-flex-center">
        <div class="uk-card uk-card-default uk-card-body uk-width-1-2@m">
            <?=$view->insideContent?>
        </div>
    </div>
</div>

