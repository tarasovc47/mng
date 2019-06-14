<?php

namespace frontend\modules\techsup;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'frontend\modules\techsup\controllers';
    public $defaultRoute = "dashboard/index";

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
