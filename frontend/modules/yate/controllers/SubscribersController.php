<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 01.12.18
 * Time: 20:02
 */

namespace frontend\modules\yate\controllers;


use frontend\components\FrontendComponent;

class SubscribersController extends FrontendComponent
{
    public function actionIndex(){
        return $this->render('index');
    }
}