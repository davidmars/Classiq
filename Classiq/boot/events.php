<?php

use Classiq\Models\Classiqmodel;
use Classiq\Models\Filerecord;
use Classiq\Utils\NotifyManager;
use PHPMailer\PHPMailer\PHPMailer;
use Pov\Defaults\C_povApi;
use Pov\System\ApiResponse;
use Pov\System\ServerEvent;

//SAVE
pov()->events->listen(C_povApi::EVENT_SAVE,
    function(ApiResponse $vv){

        if(!the()->human->isAdmin){
            cq()->notify->human->logout();
            $vv->addError("Vous devez vous connecter");
        }

        $id=$vv->testAndGetRequest("modelId","Il nous faut un modelId",true);
        $type=$vv->testAndGetRequest("modelType","Il nous faut un modelType",true);
        $vars=$vv->testAndGetRequest("modelVars");
        //pov()->log->debug("PovApi.save",[$vars]);
        /** @var Classiqmodel $record */
        $record=null;
        if($vv->success){
            $record=db()->load($type,$id)->box();
            if(!$record){
                $vv->addError("record introuvable");
            }
        }
        if($vv->success){
            foreach ($vars as $k=>$v){
                $record->setValue($k,$v);
            }
            try{
                db()->store($record);
            }catch(\Exception $exception){
                $vv->addError($exception->getMessage());
            }
        }

        if($vv->success){
            $options=the()->request("options");
            if($options){
                foreach ($options as $option=>$optionValue){
                    switch ($option){
                        //va retourner le contenu de la liste sous forme de code html
                        case "returnListItems":
                            $vv->addToJson(
                                "htmlItems",
                                $record->wysiwyg()
                                ->field($optionValue)
                                ->listJson()
                                ->_htmlItems()
                            );
                            break;
                        default:
                            $vv->addError("option non prise en charge $option");
                    }
                }
            }
        }

    }
);

//CREATE
pov()->events->listen(C_povApi::EVENT_CREATE,
    function(ApiResponse $vv){

        if(!the()->human->isAdmin){
            cq()->notify->human->logout();
            $vv->addError("Vous devez vous connecter");
        }

        $type=$vv->testAndGetRequest("modelType","Il nous faut un modelType",true);
        $vars=$vv->testAndGetRequest("modelVars");
        if($vv->success){
            $type=strtolower($type);
            /** @var Classiqmodel $record */
            $record=db()->dispense($type)->box();
            foreach ($vars as $k=>$v){
                $record->$k=$v;
            }

            $e=$record->getErrors();
            if($e){
                foreach ($e as $field=>$message)
                {
                   $vv->addError($message);
                }
            }

            if($vv->success){
                try{
                    db()->store($record);
                    $vv->addToJson("recordCreated",$record->apiData());
                }catch(\Exception $exception){
                    $vv->addError($exception->getMessage());
                }
                cq()->notify->admins->notify(Classiqmodel::EVENT_SSE_DB_COUNT_CHANGE,"Nouveau ".\Classiq\Models\ClassicModelSchema::humanType($type)." vient d'être créé");
            }
        }
    }
);

//DELETE
pov()->events->listen(C_povApi::EVENT_DELETE,
    function(ApiResponse $vv){

        if(!the()->human->isAdmin){
            cq()->notify->human->logout();
            $vv->addError("Vous devez vous connecter");
        }
        $uid=$vv->testAndGetRequest("uid");
        $record=Classiqmodel::getByUid($uid);
        if(!$record){
            $vv->addError("Impossible de trouver ce record ($uid)");
        }
        if($vv->success){
            try{
                db()->trash($record->unbox());
            }catch(\Exception $exception){
                $vv->addError($exception->getMessage());
            }
        }

    }
);

//UPLOAD
pov()->events->listen(C_povApi::EVENT_UPLOAD,
    function(ApiResponse $vv){
        if(!the()->human->isAdmin){
            cq()->notify->human->logout();
            $vv->addError("Vous devez vous connecter");
        }
        //si on recoit $fileIdentifier alors on fait juste  un test voir si le fichier existe déjà.
        $fileIdentifier=the()->request("fileIdentifier");
        if($fileIdentifier){
            $fileRecord=Filerecord::getExistingByFileIdentifier($fileIdentifier);
            if($fileRecord){
                $vv->addToJson("record",$fileRecord->apiData());
            }else{
                $vv->addError("Ce fichier n'est pas encore dans la DB");
            }
            return;
        }
        $filename=$vv->testAndGetRequest("filename");

        if($vv->success){
            //3 types d'upload possibles
            if(isset($_FILES["file"])){
                //via champ classique nommé
                $file=the()->fileSystem->uploadFromHtmlInput();
            }elseif(isset($_FILES["chunck"])){
                //html5 uploader chuncked
                $filenametmp=the()->request("filenametmp");
                $size=the()->request("size");
                $end=the()->request("end");
                $vv->addToJson("filename",$filename);
                $vv->addToJson("progress",100/$size*$end."%");
                $chunk_tmp_name = $_FILES['chunck']['tmp_name'];
                $tmpfile=the()->fileSystem->tmpPath."/".pov()->utils->string->clean($filenametmp,"-_.");
                // Open temp file
                $out = fopen($tmpfile, "a+");
                if ( $out ) {
                    // Read binary input stream and append it to temp file
                    $chunck = fopen($chunk_tmp_name, "rb");
                    if ( $chunck ) {
                        while ( $buff = fread( $chunck, 1048576 ) ) {
                            fwrite($out, $buff);
                        }
                    }
                    fclose($chunck);
                    fclose($out);
                }
                if($size===$end){
                    $file=the()->fileSystem->uploadLocalPath(date("Y/m/d/h-i-s-").pov()->utils->string->clean($filename,"-_."));
                    the()->fileSystem->prepareDir($file);
                    rename($tmpfile,$file);
                }else{
                    $file="";
                    return;
                }
            }else{
                //via html 5 uploader
                $file=the()->fileSystem->uploadFromStream();
            }
            $vv->addToJson("file",$file);

            if($file && file_exists($file) && is_file($file)){
                $record=Filerecord::getExistingByFile($file);
                if(!$record || !$record->isOk()) {
                    $vv->addMessage("uploadé dans $file");
                    /** @var Filerecord $record */
                    $record=Filerecord::fromFile($file);
                    if($record){
                        db()->store($record);
                    }else{
                        $vv->addError("problème pour enregistrer ce fichier");
                    }


                }else{
                    $vv->addMessage("fichier existait déjà ;)");
                }
                $vv->addToJson("record",$record->apiData());
            }else{
                $vv->addError("pas uploadé :(");
            }
        }
    }
);

//GET VIEW
pov()->events->listen(C_povApi::EVENT_GET_VIEW,
    function(ApiResponse $vv){
        $v=the()->request("viewPath");
        $uid=the()->request("uid");
        if($uid && $v){
            $model=Classiqmodel::getByUid($uid);
            if($model){
                $vv->addToJson("model",$model);
                $vv->setHtml($v,$model);
            }
        }
    }
);
//GET ACTION
pov()->events->listen(C_povApi::EVENT_ACTION,
    function(ApiResponse $vv,string $actionName){

        /*
        //exemple qui enregistre un formulaire contact
        $vv->addMessage("Hello action $actionName");
        switch ($actionName){
            case "formContact":
                $formDbRecord=db()->dispense("formulairecontact");
                $formDbRecord->date_envoi=pov()->utils->date->nowMysql();
                $formDbRecord->nom=$nom=the()->request("nom");
                $formDbRecord->email=$emailFrom=the()->request("email");
                $formDbRecord->message=$message=the()->request("message");
                db()->store($formDbRecord);

                $mailBody="";

                $mailBody.="<b>Nom</b><br>";
                $mailBody.="$nom<br><br>";
                $mailBody.="<b>Email</b><br>";
                $mailBody.="$emailFrom<br><br>";
                $mailBody.="<b>Message</b><br>";
                $mailBody.="$message<br><br>";
                $mailTo=the()->request("mailto");
                if($mailTo){
                    try {
                        cq()->sendMail(
                            $mailTo,
                            '[FORMULAIRE CONTACT SITE FK ' . fk()->name . '] ' . $nom,
                            $mailBody
                        );
                    }catch (Exception $e) {
                        $vv->addToJson("mailResult","nok ".$e->getMessage());
                    }
                }else{
                    $vv->addError("Personne ne recevra de mail pour ce formulaire");
                }
                break;
            default:
                $vv->addError("Action $actionName pas prise en charge par ".C_povApi::EVENT_ACTION);
        }
        */
    }
);

//SSE
pov()->events->listen(C_povApi::EVENT_REQUEST_SSE_INIT,
    function(){
        if(cq()->wysiwyg()){
            //ServerEvent::$retry=3*1000;
        }else{
            //ServerEvent::$retry=10*1000;
        }
        //permettra de filtrer ce qui faut afficher ou pas de la boucle qui va suivre
        NotifyManager::SSEloopStart();
        db()->useWriterCache(false);
    }
);
pov()->events->listen(C_povApi::EVENT_REQUEST_SSE_LOOP,
    function(){

        //de temps en temps on vérifie si on est loggué ou pas
        if(rand(0,100)>90){
            new ServerEvent(uniqid(),NotifyManager::SSE_USER_IS_WYSIWYG,'',
                [
                    "wysiwyg"=>cq()->wysiwyg(false)
                ]
            );
        }
        NotifyManager::notifyToServerEvents();

        /**
        if(rand(0,1000)>900){
            $m="<b>Salut!</b> 
                <br> il est <time>".date("H:i:s")."</time>. ";
                new ServerEvent(uniqid(),NotifyManager::EVENT_INFO, $m);
        }
        **/
    }
);
