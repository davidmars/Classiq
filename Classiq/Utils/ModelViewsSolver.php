<?php

namespace Classiq\Utils;

use Classiq\Models\Classiqbean;
use Classiq\Models\Classiqmodel;
use Pov\Html\Trace\HtmlTag;
use Pov\MVC\View;
use Pov\System\ApiResponse;

/**
 * La classe ModelViewsSolver permet de gérer les vues par défaut d'un modèle
 * @package Classiq\Utils
 */
class ModelViewsSolver
{
    /**
     * @var Classiqbean
     */
    private $model;
    /**
     * ModelViewsSolver constructor.
     * @param Classiqbean $model
     */
    public function __construct($model)
    {
        $this->model=$model;
    }

    /**
     * Renvoie le chemin de la vue (qui vient du champ view ou de records/maclasse.page.php
     * @return mixed|string
     */
    public function getViewPath($extension){
        $extension=trim($extension,".");
        $path=$this->model->view;
        if($path && $extension!="page"){
            $path="$path.$extension";
        }
        if(!$path){
            $path="records/".$this->model->modelType().".$extension";
        }
        return $path;
    }

    /**
     * Vue de la page relative à ce modèle
     * @return View
     * @throws \Exception
     */
    public function page(){
        $path=$this->getViewPath("page");
        $v=View::get($path,$this->model);
        if(the()->requestUrl->isAjax){
            //renvoie un json qui sera utilisé par PovHistory.js
            $obj=new ApiResponse();
            $obj->addToJson("meta",the()->htmlLayout()->meta);
            $obj->addToJson("pageInfo",the()->htmlLayout()->pageInfo);

            $obj->html=$v->render();
            $v->inside("json",$obj);
            return View::get("json",$obj,true);
        }else{
            return $v;
        }
    }

    public function wysiwygPreview(){
        $possibles=$this->getClassHierarchy();

        foreach ($possibles as $model){
            $p="records/$model.wysiwyg.preview";
            if(View::isValid($p)){
                return View::get($p,$this->model);
            }
        }
        $p="records/default.wysiwyg.preview";
        return View::get($p,$this->model);
    }

    /**
     * Renvoie la hierarchie de classes (lowercase)
     * @return string[]
     */
    public function getClassHierarchy(){

        $calledModel=get_class($this->model);
        $classesHierarchy=pov()->utils->phpAnalyzer->getClassHierarchy($calledModel);
        $classesHierarchy=array_filter($classesHierarchy,
            //on vire les classes qui sont pas dans notre délire de Classiq page
            function($classNamespaced){
                return preg_match("/Classiq\\\Models/",$classNamespaced,$m)!=0;
            });
        $classesHierarchy = array_map(function($k){
            return strtolower(pov()->utils->phpAnalyzer->getClassWithoutNameSpaces($k));
        }, $classesHierarchy);

        return $classesHierarchy;
    }

    /**
     * Un élément html qui contient toutes les boites de config du modèle héritées de la hiérarchies de classes.
     * @return string Le panneau de configuration de la page dans le big menu en d'autres termes :)
     */
    public function wysiwygBoxes(){
        if(!cq()->wysiwyg()){
            return "";
        }else{
            return View::get("records/record-boxes",$this->model)->render();
        }

    }

    /**
     * @param string $modelClassName
     * @return null|View
     */
    public function configByClass($modelClassName){
        $path="records/".$modelClassName.".config";
        if(!View::isValid($path)){
            return null;
        }else{
            return View::get($path,$this->model);
        }
    }




}