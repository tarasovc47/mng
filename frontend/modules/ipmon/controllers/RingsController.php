<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 09.12.16
 * Time: 11:50
 */

namespace frontend\modules\ipmon\controllers;
use frontend\components\FrontendComponent;

class RingsController extends FrontendComponent
{
    public function beforeAction($action){
        $this->view->title = "IpMon | Кольца";
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}