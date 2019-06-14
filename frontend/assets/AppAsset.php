<?php

namespace frontend\assets;

use function GuzzleHttp\default_ca_bundle;
use Yii;
use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/sb-admin.css',
        'css/style.css',
    ];
    public $js = [
        'js/script.js',
    ];
    public $depends = [
        'yii\web\YiiAsset', // yii.js, jquery.js
        'yii\bootstrap\BootstrapAsset', // bootstrap.css
//        'yii\bootstrap4\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset', // bootstrap.js
        'yii\jui\JuiAsset', //
        'common\assets\LibJsAsset', // Наши библиотеки и функции
    ];

    public function __construct()
    {
        parent::__construct();

        $module = Yii::$app->controller->module->id;
        $controller = Yii::$app->controller->id;
        $action = Yii::$app->controller->action->id;

        switch($module){
            case "tools":
                switch($controller){
                    case 'converter':
                        $this->js[] = 'js/tools/converter.js';
                        break;

                    case 'swconf':
                        $this->js[] = 'js/tools/swconf.js';
                        break;

                    case 'voip':
                        $this->js[] = 'js/tools/voip.js';
                        break;

                    case 'raspberry':
                        $this->js[] = 'js/tools/rasp.js';
                        break;

                    case 'urls':
                        $this->js[] = 'js/tools/urls.js';
                        break;
                }
                break;
            case "techsup":
                $this->css[] = 'css/techsup/techsup.css';

                if(($controller == "applications") && ($action == "create")){
                    $this->js[] = 'js/techsup/applications-create.js';
                }

                if(($controller == "dashboard") && ($action == "brigadier")){
                    $this->js[] = 'js/techsup/dashboard-brigadier.js';
                }

                if(($controller == "dashboard") && ($action == "engineer")){
                    $this->js[] = 'js/techsup/dashboard-engineer.js';
                }

                if(($controller == "dashboard") && ($action == "nod")){
                    $this->js[] = 'js/techsup/dashboard-nod.js';
                }

                if(($controller == "dashboard") && ($action == "support")){
                    $this->js[] = 'js/techsup/dashboard-support.js';
                }
                break;
            case "radius":

                switch($controller){
                    case "pppoe":
                        $this->js[] = 'js/radius/main.js';
                        $this->js[] = 'js/radius/sessions.js';
                        $this->css[] = 'css/radius/sessions.css';
                        break;

                    default:
                        $this->js[] = 'js/radius/radius.js';
                        $this->css[] = 'css/radius/sessions.css';
                        break;
                }
               
                break;

            case "sessions":
                switch($controller){
                    case 'default':
                        $this->js[] = 'js/sessions/sessions.js';
                        $this->js[] = 'js/sessions/accounting.js';
                        break;

                    case 'accounting':
                        $this->js[] = 'js/sessions/accounting.js';
                        break;

                    case 'archive':
                        $this->js[] = 'js/sessions/archive.js';
                        break;
                }

                $this->css[] = 'css/sessions/styles.css';
                break;
                
            case "ipmon":
                switch($controller){
                    case 'arps':
                        $this->js[] = 'js/ipmon/arps.js';
                        $this->css[] = 'css/ipmon/arps.css';
                        break;

                    case 'arptables':
                        $this->js[] = 'js/ipmon/arptables.js';
                        $this->css[] = 'css/ipmon/arps.css';
                        $this->css[] = 'css/ipmon/ipmon.css';
//                        $this->css[] = 'css/ipmon/arptables.css';
                        break;

                    case 'backbone':
                        $this->js[] = 'js/ipmon/backbone.js';
//                        $this->css[] = 'js/ipmon/fancytree/skin-bootstrap/ui.fancytree.css';
//                        $this->js[] = 'js/ipmon/fancytree/jquery.fancytree-all.min.js';
//                        $this->js[] = 'js/bootstrap-treeview.min.js';
//                        $this->css[] = 'css/bootstrap-treeview.min.css';
                        $this->css[] = 'css/ipmon/ipmon.css';
                        break;

                    default:
                        $this->css[] = 'js/ipmon/fancytree/skin-bootstrap/ui.fancytree.css';
                        $this->css[] = 'css/ipmon/ipmon.css';
//                        $this->js[] = 'js/ipmon/fancytree/jquery.fancytree-all.min.js';
                        $this->js[] = 'js/ipmon/ipmon.js';
//                        $this->js[] = 'js/ipmon/d3.js';
//                        $this->js[] = 'js/ipmon/d3test.js';
                        break;
                }
                break;

            case "zones":
                switch($controller){
                    case 'zones-addresses':
                        $this->css[] = 'css/zones/zonesAddresses.css';
                        $this->js[] = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU';
                        $this->js[] = 'js/zones/zonesAddresses.js';
                        break;
                    case 'access-agreements':
                        $this->css[] = 'css/zones/accessAgreements.css';
                        $this->js[] = 'js/zones/accessAgreements.js';
                        break;
                    case 'districts-and-areas':
                        $this->js[] = 'js/zones/districtsAndAreas.js';
                        break;
                    case 'poligons':
                        $this->js[] = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU';
                        $this->js[] = 'js/zones/poligons.js';
                        break;
                    default:
                        break;
                }
                break;
                
            default:
                break;
        }

        switch ($controller) {
            case 'abonent':
                $this->css[] = 'css/abonent/abonent.css';
                $this->js[] = 'js/abonent/abonent.js';
                break;
            case 'docs-archive':
                $this->css[] = 'css/abonent/docsArchive.css';
                $this->js[] = 'js/abonent/docsArchive.js';
                break;
            case 'client-search':
                $this->css[] = 'css/clientSearch.css';
                $this->js[] = 'js/clientSearch.js';
                break;
            case 'manag-companies':
                $this->css[] = 'css/managCompanies.css';
                $this->js[] = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU';
                $this->js[] = 'js/jquery.maskedinput.min.js';
                $this->js[] = 'js/managCompanies.js';
                break;
            case 'contact-faces':
                $this->js[] = 'js/jquery.maskedinput.min.js';
                $this->js[] = 'js/contactFaces.js';
                break;
            case 'user':
                $this->css[] = 'css/user.css';
                $this->js[] = 'js/user.js';
                break;
            case 'tariffs':
                $this->css[] = 'css/tariffs/tariffs.css';
                $this->js[] = 'js/tariffs/tariffs.js';
                break;
            case 'tariffs-groups':
                $this->js[] = 'js/tariffs/tariffs-groups.js';
                break;
            case 'addresses-recycle':
                $this->js[] = 'js/addresses-recycle.js';
                break;
            default:
                break;
        }
    }
}
