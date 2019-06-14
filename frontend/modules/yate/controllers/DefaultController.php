<?php

namespace frontend\modules\yate\controllers;
use frontend\components\FrontendComponent;

/**
 * Default controller for the `yate` module
 */
class DefaultController extends FrontendComponent
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
