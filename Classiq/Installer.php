<?php

namespace Classiq;

class Installer
{
    /**
     * Crée le répertoire dist
     */
    public static function install(){
        if(!is_dir("dist")){
            mkdir("dist");
        }
    }
}