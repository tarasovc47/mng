<?php

namespace backend\modules\zones;

/**
 * zones module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\zones\controllers';
    public $defaultRoute = 'address-types';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
