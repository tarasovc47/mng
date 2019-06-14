<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 01.12.18
 * Time: 20:03
 */

namespace frontend\modules\yate\controllers;


use frontend\components\FrontendComponent;

class RoutingController extends FrontendComponent
{
    public function actionIndex(){
        return $this->render('index');
    }
}