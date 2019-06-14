<?php

namespace backend\modules\techsup;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'backend\modules\techsup\controllers';
    public $defaultRoute = 'attributes';

    public function init()
    {
        parent::init();
    }
}
