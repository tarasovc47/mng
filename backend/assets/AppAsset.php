<?php

namespace backend\assets;

use Yii;
use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\jui\JuiAsset',
        'common\assets\LibJsAsset',  // Наши библиотеки и функции
    ];

    public function __construct()
    {
        parent::__construct();

        $module = Yii::$app->controller->module->id;
        $controller = Yii::$app->controller->id;
        $action = Yii::$app->controller->action->id;

        switch($module){
            case "techsup":
                $this->css[] = 'css/techsup/techsup.css';

                if(($controller == "fields") && (($action == "create") || ($action == "update"))){
                    $this->js[] = 'js/techsup/fields.js';
                }

                if(($controller == "scenarios") && (($action == "create") || ($action == "update"))){
                    $this->js[] = 'js/techsup/scenarios.js';
                }

                if(($controller == "brigades") && (($action == "create") || ($action == "update"))){
                    $this->js[] = 'js/techsup/brigades.js';
                }
                break;
            default:
        }

        if($controller == "attributes"){
            $this->js[] = 'js/attributes.js';
        }

        if($controller == "properties"){
            $this->js[] = 'js/properties.js';
        }

        if(($controller == "docs-types") && (($action == "create"))){
            $this->js[] = 'js/docs-types.js';
        }

        if(($controller == "departments") && (($action == "update"))){
            $this->js[] = 'js/departments.js';
            $this->css[] = 'css/departments.css';
        }

        if(($controller == "cas-user") && (($action == "update"))){
            $this->js[] = 'js/cas-user.js';
        }

        if($controller == "contacts-offices"){
            $this->js[] = 'js/contacts-offices.js';
        }
    }
}
