<?php

namespace backend\modules\techsup\controllers;

use backend\components\BackendComponent;

class DefaultController extends BackendComponent
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
